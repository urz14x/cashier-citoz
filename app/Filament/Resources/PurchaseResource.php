<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Filament\Resources\PurchaseResource\RelationManagers;
use App\Models\Purchase;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;
    protected static ?string $navigationGroup = 'Menu Transaksi';
    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    public static function getNavigationLabel(): string
    {
        return 'Pengeluaran';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('item_name')
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->label('Harga ')
                    ->required()
                    ->numeric()
                    ->mask(RawJs::make('$money($input)')) // opsional
                    ->stripCharacters([',']) // ← ini bagian penting
                    ->prefix('Rp'),
                Forms\Components\Select::make('payment_method')
                    ->enum(\App\Enums\PaymentMethod::class)
                    ->options(\App\Enums\PaymentMethod::class)
                    ->default(\App\Enums\PaymentMethod::CASH)
                    ->required(),
                Forms\Components\DatePicker::make('purchase_date')
                    ->label('Tanggal pembelian')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->label('Kategori')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Pegawai')
                    ->numeric()
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('item_name')->label('Nama Pegawai')->searchable(),
                Tables\Columns\TextColumn::make('quantity')->label('Kuantitas')->badge(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->numeric()
                    ->prefix('Rp ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')->label('Metode pembayaran')->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'cash' => 'success',
                        'bank_transfer' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('purchase_date')->color('success'),
                Tables\Columns\TextColumn::make('notes')
                    ->limit(50)
                    ->color('gray')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('price')
                    ->numeric()
                    ->sortable()
                    ->alignEnd()

                    ->summarize(
                        Tables\Columns\Summarizers\Sum::make('price')
                            ->money('IDR'),
                    ),
            ])
            ->filters([
                Filter::make('purchase_date_range')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->where('purchase_date', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->where('purchase_date', '<=', $data['until']));
                    })
                    ->label('Tanggal Pembelian'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }
}
