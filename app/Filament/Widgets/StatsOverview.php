<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Purchase;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = 'full';
    protected function getStats(): array

    {
        $totalThisMonth = Purchase::whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->sum('price');

        $totalSenamOrders = OrderDetail::whereHas('product.category', function ($query) {
            $query->where('name', 'Senam');
        })
            ->whereHas('order', fn($q) => $q->where('status', 'completed'))
            ->count();
        $totalIncomeSenam = OrderDetail::whereHas('product.category', function ($query) {
            $query->where('name', 'senam');
        })
            ->whereHas('order', fn($q) => $q->where('status', 'completed'))
            ->sum('subtotal');

            $cashTotal = Order::where('payment_method', 'cash')
            ->where('status', 'completed')
            ->sum('total');

        $tfTotal = Order::where('payment_method', 'bank_transfer')
            ->where('status', 'completed')
            ->sum('total');

        return [
            Stat::make('Keseluruhan Pengeluaran Bulan Ini', 'Rp ' . number_format($totalThisMonth, 0, ',', '.'))
                ->description('Total pembelian bulan ' . now()->translatedFormat('F'))
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),
            Stat::make('Pemasukan Keseluruhan Bulan Ini', 'Rp ' . number_format(Order::sum('total'), 0, ',', '.'))
                ->description('Dari semua order')
                ->color('success'),
            Stat::make('Total Pemasukan Senam', 'Rp ' . number_format($totalIncomeSenam, 0, ',', '.'))
                ->description('Pemasukan dari semua produk senam')
                ->color('warning')
                ->descriptionIcon('heroicon-o-banknotes'),
            Stat::make('Total Order Senam', $totalSenamOrders . ' Transaksi')
                ->description('Transaksi senam yang sudah dibayar')
                ->color('success')
                ->descriptionIcon('heroicon-o-chart-bar'),
                Stat::make('Pemasukan Order Tunai', 'Rp ' . number_format($cashTotal, 0, ',', '.'))
                ->description('Pembayaran dengan Cash')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Pemasukan Order Non Tunai', 'Rp ' . number_format($tfTotal, 0, ',', '.'))
                ->description('Pembayaran dengan Transfer')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('warning'),
        ];
    }
    protected function getColumns(): int
    {
        return 3; // Maksimum 3 stat per baris
    }
}
