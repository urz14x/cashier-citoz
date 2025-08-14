<?php

namespace App\Filament\Resources;

use App\Enums\MemberStatus;
use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use App\Filament\Exports\MemberExporter;
use App\Filament\Imports\MemberImporter;
use App\Filament\Resources\MemberResource\Pages\ViewMember;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;
    protected static ?string $navigationGroup = 'Menu Transaksi';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('package_id')
                    ->relationship('package', 'name')
                    ->label('Jenis Paket')
                    ->searchable()
                    ->required()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $joined = $get('joined');
                        $expired = $get('expired');

                        if (! $joined || ! $state) {
                            return;
                        }

                        $package = \App\Models\Package::find($state);
                        if (! $package) return;

                        $duration = $package->duration_in_day;

                        $now = now();

                        // Logika perpanjangan
                        $baseDate = $joined;

                        // Jika expired masih di masa depan, perpanjang dari expired
                        if ($expired && \Carbon\Carbon::parse($expired)->gt($now)) {
                            $baseDate = $expired;
                        } else {
                            $baseDate = \Carbon\Carbon::parse($joined);
                        }

                        $newExpired = \Carbon\Carbon::parse($baseDate)->addDays($duration)->format('Y-m-d');

                        $set('expired', $newExpired);
                    }),

                Forms\Components\Select::make('personal_trainers')
                    ->label('Personal Trainers')
                    ->relationship('personalTrainers', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Member'),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255)
                    ->label('Alamat'),
                Forms\Components\TextInput::make('social_media')
                    ->required()
                    ->maxLength(255)
                    ->label('Sosial Media'),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(15)
                    ->label('No. Handphone'),
                Forms\Components\Select::make('gender')
                    ->options([
                        'M' => 'Pria',
                        'F' => 'Wanita'
                    ])
                    ->label('Jenis Kelamin')
                    ->required(),
                DatePicker::make('joined')
                    ->label('Tanggal Pendaftaran')
                    ->required()
                    ->maxDate(Carbon::now())
                    ->minDate(Carbon::now()->subYears(5))
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $packageId = $get('package_id');
                        if ($state && $packageId) {
                            $duration = \App\Models\Package::find($packageId)?->duration_in_day ?? 0;
                            $expired = Carbon::parse($state)->addDays($duration)->format('Y-m-d');
                            $set('expired', $expired);
                        }
                    })
                    ->label('Bergabung'),
                TextInput::make('expired')
                    ->label('Tanggal Expired')
                    ->readOnly()
                    ->disabled()
                    ->dehydrated() // pastikan tetap dikirim ke database
                    ->required()
                    ->label('Berlaku sampai'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Member')
                    ->searchable(),
                Tables\Columns\TextColumn::make('package.name')
                    ->label('Jenis Paket')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$record->joined || !$record->expired) {
                            return '-';
                        }

                        $totalDays = \Carbon\Carbon::parse($record->joined)->diffInDays(\Carbon\Carbon::parse($record->expired));

                        // Cari paket yang cocok persis dengan total hari
                        $matched = \App\Models\Package::where('duration_in_day', $totalDays)->first();

                        // Jika ada, tampilkan nama paket dari database
                        if ($matched) {
                            return $matched->name;
                        }

                        // Jika tidak ada yang cocok, hitung manual sebagai "X Bulan" atau "X Hari"
                        if ($totalDays % 30 === 0) {
                            return 'Paket 1 Bulan';
                        }

                        return $totalDays . ' Hari';
                    })
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => $state instanceof \App\Enums\MemberStatus ? $state->getColor() : 'secondary')
                    ->formatStateUsing(fn($state) => $state instanceof \App\Enums\MemberStatus ? $state->getLabel() : $state),

                Tables\Columns\TextColumn::make('joined')
                    ->date()
                    ->label('Bergabung')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expired')
                    ->date()
                    ->label('Berakhir')
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('No Hp')
                    ->searchable(),

                Tables\Columns\TextColumn::make('has_transaction')
                    ->label('Status Transaksi')
                    ->badge()
                    ->colors([
                        'success' => fn($state) => $state === true,
                        'danger' => fn($state) => $state === false,
                    ])
                    ->formatStateUsing(fn($state) => $state ? 'Sudah Transaksi' : 'Belum Transaksi'),


                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('qr_code')
                    ->label('Lihat QR')
                    ->modalHeading('QR Code Membership')
                    ->icon('heroicon-o-qr-code')
                    ->modalContent(function ($record) {
                        $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)->margin(2)->generate($record->qr_code);

                        return view('filament.components.qr-modal-member', [
                            'qrSvg' => $qrSvg,
                            'memberName' => $record->name,
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label('Export')
                    ->fileDisk('public')
                    ->color('success')
                    ->icon('heroicon-o-document-text')
                    ->exporter(MemberExporter::class),
                // Tables\Actions\ImportAction::make()
                //     ->label('Import Member')
                //     ->icon('heroicon-o-arrow-up-tray')
                //     ->importer(MemberImporter::class),
            ]);
    }
    public static function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Member')
                    ->description('Detail member dan informasi keanggotaannya.')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama')
                            ->icon('heroicon-m-user')
                            ->badge()
                            ->color('primary')
                            ->weight('bold'),
                        TextEntry::make('has_transaction')
                            ->label('Status Transaksi')
                            ->formatStateUsing(fn($state) => $state ? 'Sudah Transaksi' : 'Belum Transaksi')
                            ->icon(fn($state) => $state ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                            ->color(fn($state) => $state ? 'success' : 'danger'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn($state) => $state instanceof \App\Enums\MemberStatus ? $state->getColor() : 'secondary')
                            ->formatStateUsing(fn($state) => $state instanceof \App\Enums\MemberStatus ? $state->getLabel() : $state),

                        TextEntry::make('package.name')
                            ->label('Paket')
                            ->icon('heroicon-m-cube')
                            ->color('info'),

                        TextEntry::make('gender')
                            ->label('Jenis Kelamin')
                            ->icon('heroicon-m-user')
                            ->formatStateUsing(fn($state) => $state instanceof \App\Enums\Gender ? $state->label() : 'Tidak Diketahui')


                            ->color('info'),
                        TextEntry::make('phone')
                            ->label('Nomor Telepon')
                            ->icon('heroicon-m-phone'),

                        TextEntry::make('address')
                            ->label('Alamat')
                            ->icon('heroicon-m-map-pin'),

                        TextEntry::make('joined')
                            ->label('Bergabung')
                            ->icon('heroicon-m-calendar')
                            ->date('d M Y'),

                        TextEntry::make('expired')
                            ->label('Berlaku Sampai')
                            ->icon('heroicon-m-clock')
                            ->date('d M Y')
                            ->color(fn($state) => \Carbon\Carbon::parse($state)->isPast() ? 'danger' : 'success'),
                    ]),

                Section::make('Informasi Sistem')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Tanggal Dibuat')
                            ->dateTime('d M Y H:i')
                            ->icon('heroicon-m-pencil-square'),

                        TextEntry::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->dateTime('d M Y H:i')
                            ->icon('heroicon-m-arrow-path'),
                    ]),
            ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMembers::route('/'),
            'view' => ViewMember::route('/{record}'),
        ];
    }
    public static function getNavigationLabel(): string
    {
        return 'Pendaftaran Member';
    }
}
