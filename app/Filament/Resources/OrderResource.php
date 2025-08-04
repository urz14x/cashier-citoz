<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Filament\Exports\OrderExporter;
use App\Filament\Resources\OrderResource\Pages;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationGroup = 'Menu Transaksi';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_number')
                    ->required()
                    ->default(generateSequentialNumber(Order::class))
                    ->readOnly(),
                Forms\Components\Select::make('member_id')
                    ->relationship('member', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->reactive()
                    ->label('Pilih Member (optional)')
                    ->placeholder('Pilih Member')
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Hanya kosongkan PT jika member berubah
                        $set('personalTrainers', []);
                    }),
                    Forms\Components\Placeholder::make('existing_pt_info')
                    ->label('Personal Trainer yang pernah disewa')
                    ->content(function (callable $get) {
                        $memberId = $get('member_id');

                        if (!$memberId) return 'Silakan pilih member terlebih dahulu.';

                        $member = \App\Models\Member::with('personalTrainers')->find($memberId);
                        if (!$member || $member->personalTrainers->isEmpty()) return 'Belum pernah sewa PT.';

                        return $member->personalTrainers->pluck('name')->join(', ');
                    })
                    ->visible(fn(callable $get) => filled($get('member_id')))
                    ->columnSpanFull()
                    ->reactive(),

                // Forms\Components\Select::make('personalTrainers')
                //     ->label('Personal Trainers')
                //     ->relationship('personalTrainers', 'name')
                //     ->searchable()
                //     ->multiple()
                //     ->preload()
                //     ->disabled(fn($get) => !$get('member_id'))
                //     ->reactive()
                //     ->afterStateUpdated(function ($state, callable $set, callable $get) {
                //         // ✅ Hitung ulang total setiap kali PT berubah
                //         $pts = \App\Models\PersonalTrainer::whereIn('id', $state)->get();
                //         $trainerPrice = $pts->sum('price_per_visit');

                //         $baseTotal = intval($get('base_total') ?? 0);
                //         $discount = intval($get('discount') ?? 0);

                //         $set('total', $baseTotal + $trainerPrice - $discount);
                //     }),

                Forms\Components\TextInput::make('order_name')
                    ->maxLength(255)
                    ->placeholder('Tulis nama pesanan'),

                Forms\Components\TextInput::make('discount')
                    ->numeric()
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        static::updateTotalPT($get, $set);
                    }),
                Forms\Components\TextInput::make('total')
                    ->label('Total')
                    ->readOnly()
                    ->numeric()
                    ->default(0)
                    ->reactive(),
                Forms\Components\TextInput::make('base_total')
                    ->default(0)
                    ->numeric()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        static::updateTotalPT($get, $set); // ✅ Gunakan fungsi helper yang sudah kamu buat
                    }),

                Forms\Components\Group::make([
                    Forms\Components\Select::make('payment_method')
                        ->enum(\App\Enums\PaymentMethod::class)
                        ->options(\App\Enums\PaymentMethod::class)
                        ->default(\App\Enums\PaymentMethod::CASH)
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->label('Status Order')
                        ->options(OrderStatus::class) // akan otomatis ambil dari enum yang implement HasLabel
                        ->required()
                        ->default(OrderStatus::PENDING)

                ])->columns(2),

                Forms\Components\Repeater::make('personalTrainers')
                    ->label('Personal Trainers')
                    ->visible(fn (callable $get) => filled($get('member_id')))
                    ->schema([
                        Forms\Components\Select::make('personal_trainer_id')
                            ->label('Pilih PT')
                            ->options(\App\Models\PersonalTrainer::pluck('name', 'id'))
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(
                                fn($state, callable $set, callable $get) =>
                                \App\Filament\Resources\OrderResource::updateTotalPT($get, $set)
                            ),

                        Forms\Components\Select::make('pt_type')
                            ->label('Jenis Sewa')
                            ->options([
                                'per_visit' => 'Per Visit',
                                'per_bulan' => 'Per Bulan',
                            ])
                            ->default('per_visit')
                            ->reactive()
                            ->afterStateUpdated(
                                fn($state, callable $set, callable $get) =>
                                \App\Filament\Resources\OrderResource::updateTotalPT($get, $set)
                            ),
                    ])
                    ->afterStateHydrated(function (Forms\Components\Repeater $component, $state) {
                        // Jika sedang edit, isi repeater dengan PT yang tersimpan
                        $record = $component->getContainer()->getRecord();
                        if (!$record) return;

                        $repeaterData = $record->personalTrainers->map(function ($pt) {
                            return [
                                'personal_trainer_id' => $pt->id,
                                'pt_type' => $pt->pivot->pt_type,
                            ];
                        })->toArray();

                        $component->state($repeaterData);
                    })
                    ->reactive()
                    ->columnSpanFull(),

            ]);
    }
    protected static function updateTotalPT(callable $get, callable $set): void
    {
        $trainerItems = $get('personalTrainers') ?? []; // ✅ Perbaiki nama key
        $baseTotal = intval($get('base_total') ?? 0);
        $discount = intval($get('discount') ?? 0);

        $totalTrainer = collect($trainerItems)->sum(function ($item) {
            $pt = \App\Models\PersonalTrainer::find($item['personal_trainer_id'] ?? null);
            if (!$pt) return 0;

            return ($item['pt_type'] ?? 'per_visit') === 'per_bulan'
                ? $pt->price_per_month
                : $pt->price_per_visit;
        });

        $set('total', max(0, $baseTotal + $totalTrainer - $discount));
    }
    public static function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('order_number')
                ->searchable(),
            Tables\Columns\TextColumn::make('user.name')
                ->label('Petugas')
                ->badge()
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->formatStateUsing(fn($state) => OrderStatus::from($state)->getLabel())
                ->color(fn($state) => OrderStatus::from($state)->getColor()),
            Tables\Columns\TextColumn::make('member.name')
                ->label('Member')
                ->badge()
                ->formatStateUsing(fn($state) => $state ?? 'Non Member'),

            Tables\Columns\TextColumn::make('order_name')
                ->searchable(),
            // Tables\Columns\TextColumn::make('discount')
            //     ->numeric()
            //     ->sortable(),
            Tables\Columns\TextColumn::make('total')
                ->numeric()
                ->sortable()
                ->alignEnd()
                ->summarize(
                    Tables\Columns\Summarizers\Sum::make('total')
                        ->money('IDR'),
                ),
            Tables\Columns\TextColumn::make('profit')
                ->numeric()
                ->alignEnd()
                ->summarize(
                    Tables\Columns\Summarizers\Sum::make('profit')
                        ->money('IDR'),
                )
                ->sortable(),
            Tables\Columns\TextColumn::make('payment_method')
                ->badge()
                ->formatStateUsing(fn($state) => PaymentMethod::from($state)->getLabel())
                ->color(fn($state) => PaymentMethod::from($state)->getColor()),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns(self::getTableColumns())
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(\App\Enums\OrderStatus::class),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->multiple()
                    ->options(\App\Enums\PaymentMethod::class),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->maxDate(fn(Forms\Get $get) => $get('end_date') ?: now())
                            ->native(false),
                        Forms\Components\DatePicker::make('created_until')
                            ->native(false)
                            ->maxDate(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->button()
                    ->color('gray')
                    ->icon('heroicon-o-printer')
                    ->action(function (Order $record) {
                        $pdf = Pdf::loadView('pdf.print-order', [
                            'order' => Order::with(['personalTrainers'])->find($record->id),
                        ]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, 'receipt-' . $record->order_number . '.pdf');
                    }),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('gray'),
                    Tables\Actions\Action::make('edit-transaction')
                        ->visible(fn($record) => $record->status === OrderStatus::PENDING->value) // hanya muncul saat pending
                        // ->button()// tampil sebagai tombol, bukan dropdown
                        ->label('Edit Transaction')
                        ->icon('heroicon-o-pencil')
                        ->url(fn($record) => "/app/orders/{$record->order_number}"),
                    Tables\Actions\Action::make('mark-as-complete')
                        ->visible(fn(Order $record) => $record->status === \App\Enums\OrderStatus::PENDING->value)
                        ->requiresConfirmation()
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Order $record) {
                            $record->status = \App\Enums\OrderStatus::COMPLETED;
                            $record->save();
                        })
                        ->label('Mark as Complete'),
                    Tables\Actions\Action::make('divider')->label('')->disabled(),
                    Tables\Actions\DeleteAction::make()
                        ->before(function (Order $order) {
                            $order->orderDetails()->delete();
                            $order->delete();
                        }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (\Illuminate\Support\Collection $records) {
                            $records->each(fn(Order $order) => $order->orderDetails()->delete());
                        }),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label('Export Excel')
                    ->fileDisk('public')
                    ->color('success')
                    ->icon('heroicon-o-document-text')
                    ->exporter(OrderExporter::class),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\OrderResource\RelationManagers\OrderDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}/details'),
            'create-transaction' => Pages\CreateTransaction::route('{record}'),
        ];
    }

    public static function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist->schema([
            TextEntry::make('order_number')->color('gray'),
            TextEntry::make('member.name')->placeholder('-')->badge(),
            TextEntry::make('discount')->money('IDR')->color('gray'),
            TextEntry::make('total')->money('IDR')->color('gray'),
            // TextEntry::make('personalTrainers')
            //     ->label('Personal Trainers')
            //     ->getStateUsing(fn($record) => $record->personalTrainers->pluck('name')->toArray())
            //     ->placeholder('-'),
            // ✅ Tambahkan bagian ini untuk melihat PT yang dimiliki member
            TextEntry::make('member.personalTrainers')
                ->label('Personal Trainer')
                ->getStateUsing(function ($record) {
                    return $record->member
                        ? $record->member->personalTrainers->pluck('name')->join(', ')
                        : '-';
                })
                ->placeholder('-'),
            TextEntry::make('payment_method')->badge()->color('gray'),
            TextEntry::make('status')->badge()->formatStateUsing(fn($state) => OrderStatus::from($state)->value)
                ->color(fn($state) => OrderStatus::from($state)->getColor()),
            TextEntry::make('created_at')->dateTime()->formatStateUsing(fn($state) => $state->format('d M Y H:i'))->color('gray'),
        ]);
    }

    public static function getWidgets(): array
    {
        return [
            OrderResource\Widgets\OrderStats::class,
        ];
    }
}
