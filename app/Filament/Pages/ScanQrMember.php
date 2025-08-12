<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ScanQrMember extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-camera';
    protected static string $view = 'filament.pages.scan-qr-member';
    protected static ?string $title = 'Scan QR Absen Member';
    protected static ?string $navigationGroup = 'Menu Member';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'manager', 'cashier']);
    }
}
