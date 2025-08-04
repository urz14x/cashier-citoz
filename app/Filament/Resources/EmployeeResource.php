<?php

namespace App\Filament\Resources;

use App\Enums\PositionEmployee;
use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;
    protected static ?string $navigationGroup = 'Menu Pegawai';
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    public static function getNavigationLabel(): string
    {
        return 'Daftar Pegawai';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Employee::count(); // tampilkan jumlah pegawai
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('birth_date')
                    ->label('Tanggal Lahir'),
                Forms\Components\Select::make('gender')
                    ->options([
                        'M' => 'Pria',
                        'F' => 'Wanita'
                    ]),
                Forms\Components\TextInput::make('social_media')
                    ->label('Sosial Media (Instagram/Facebook dll')
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('No Telepon')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Select::make('position')
                    ->label('Posisi Jabatan')
                    ->options(PositionEmployee::class),
                Forms\Components\FileUpload::make('photo')
                    ->label('Foto Profil')
                    ->image()
                    ->disk('public')
                    ->maxSize(1024)
                    ->imageCropAspectRatio('1:1')
                    ->directory('images/employee/profil')->columns(2)
                    ->columnSpan([
                        'lg' => 2,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('qr_code')
                //     ->label('QR Code')
                //     ->formatStateUsing(fn($state) => view('qr-code', ['code' => $state])),
                Tables\Columns\ImageColumn::make('photo')->label('Foto Profil')->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('social_media')
                    ->label('Media Sosial')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('No. Handphone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('position')
                    ->label('Posisi Jabatan')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('position')
                    ->label('Posisi Jabatan')
                    ->options(\App\Enums\PositionEmployee::class),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('qr_code')
                    ->label('Lihat QR')
                    ->modalHeading('QR Code Pegawai')
                    ->icon('heroicon-o-qr-code')
                    ->modalContent(function ($record) {
                        $qr = base64_encode(
                            \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(250)->generate($record->qr_code)
                        );

                        return view('filament.components.qr-modal', [
                            'qrImage' => $qr,
                            'employeeName' => $record->name,
                        ]);
                    }),
                Tables\Actions\Action::make('Kirim WhatsApp')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->url(function ($record) {
                        // Format nomor
                        $phone = ltrim($record->phone, '0');
                        $phone = "62$phone";

                        // Buat pesan teks
                        $message = urlencode(
                            "Halo {$record->name},\n" .
                                "Anda terdaftar sebagai pegawai dengan jabatan " . Str::title(str_replace('_', ' ', $record->position)) . ".\n\n" .
                                "Gunakan QR berikut untuk absen atau login:\n" .
                                route('qr.view', ['uuid' => $record->qr_code])
                        );

                        return "https://wa.me/{$phone}?text={$message}";
                    }, shouldOpenInNewTab: true)
                    ->openUrlInNewTab()
                    ->label('Whatsapp')
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
    public static function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make('Informasi Karyawan')
                    ->description('Detail data pribadi karyawan.')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Karyawan')
                            ->icon('heroicon-m-user')
                            ->weight('bold')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('gender')
                            ->label('Jenis Kelamin')
                            ->icon('heroicon-m-user-group')
                            ->formatStateUsing(fn($state) => $state === 'M' ? 'Laki-laki' : 'Perempuan'),

                        TextEntry::make('phone')
                            ->label('Nomor Telepon')
                            ->icon('heroicon-m-phone'),

                        TextEntry::make('social_media')
                            ->label('Sosial Media')
                            ->icon('heroicon-m-share'),

                        TextEntry::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->icon('heroicon-m-cake')
                            ->date('d M Y'),
                            TextEntry::make('qr_code')
                            ->label('UUID')
                            ->icon('heroicon-m-qr-code'),
                        TextEntry::make('position')
                            ->label('Jabatan')
                            ->icon('heroicon-m-briefcase')
                            ->badge()
                            ->color('info')
                            ->formatStateUsing(fn($state) => \Illuminate\Support\Str::title(str_replace('_', ' ', $state))),
                    ]),


                \Filament\Infolists\Components\Section::make('Status dan Waktu')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->icon('heroicon-m-check-circle')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'active' => 'success',
                                'inactive' => 'danger',
                                default => 'secondary',
                            })
                            ->formatStateUsing(fn($state) => \Illuminate\Support\Str::title($state)),

                        TextEntry::make('expired_at')
                            ->label('Tanggal Berakhir')
                            ->icon('heroicon-m-clock')
                            ->date('d M Y')
                            ->color(fn($state) => \Carbon\Carbon::parse($state)->isPast() ? 'danger' : 'success'),

                        TextEntry::make('created_at')
                            ->label('Tanggal Dibuat')
                            ->icon('heroicon-m-calendar')
                            ->dateTime('d M Y H:i'),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
            'view' => Pages\ViewEmployee::route('/{record}'),
        ];
    }
}
