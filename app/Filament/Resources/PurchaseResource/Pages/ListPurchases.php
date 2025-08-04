<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use App\Filament\Resources\PurchaseResource\Widgets\PurchaseStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPurchases extends ListRecords
{
    protected static string $resource = PurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getHeaderWidgets(): array
    {
        return [
            PurchaseStats::class,
        ];
    }
    public function getTitle(): string
    {
        return "Pengeluaran di Citoz Sport enter";
    }
}
