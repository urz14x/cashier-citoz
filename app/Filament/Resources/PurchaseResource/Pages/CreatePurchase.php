<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchase extends CreateRecord
{
    protected static string $resource = PurchaseResource::class;

    protected function getRedirectUrl(): string
    {
        // Langsung redirect ke halaman tabel/index setelah membuat
        return PurchaseResource::getUrl('index');
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Pengeluaran berhasil dibuat')
            ->success()
            ->send();
    }
}
