<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ScanQr extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-camera';

    protected static string $view = 'filament.pages.scan-qr';

    protected static ?string $title = 'Scan QR Pegawai';
    protected static ?string $navigationGroup = 'Menu Pegawai';
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'manager', 'cashier']); // atau auth()->user()->can('access scan qr')
    }
}
