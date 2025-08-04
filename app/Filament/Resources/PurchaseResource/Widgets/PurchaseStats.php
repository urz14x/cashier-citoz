<?php

namespace App\Filament\Resources\PurchaseResource\Widgets;

use App\Models\Order;
use App\Models\Purchase;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\TrendValue;

class PurchaseStats extends BaseWidget
{
    protected function getStats(): array
    {

        $totalThisMonth = Purchase::whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->sum('price');

        return [
            Stat::make('Pengeluaran Bulan Ini', 'Rp ' . number_format($totalThisMonth, 0, ',', '.'))
                ->description('Total pembelian bulan ' . now()->translatedFormat('F'))
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),
            // Stat::make('Total Pemasukan GYM Bulan Ini', 'Rp ' . number_format(Order::sum('total'), 0, ',', '.'))
            //     ->description('Dari semua order')
            //     ->color('success'),
        ];
    }
}
