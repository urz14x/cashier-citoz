<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Member;
use App\Models\Order;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class OrderStats extends BaseWidget
{
    use InteractsWithPageFilters;
    protected static ?string $pollingInterval = null;

    public static function canView(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'manager']);
    }

    protected function getStats(): array
    {
        $createdFrom = new Carbon($this->filters['start_date'] ?? now()->startOfMonth());
        $createdTo = new Carbon($this->filters['end_date'] ?? now()->endOfMonth());

        $query = Order::query()
            ->where('status', OrderStatus::COMPLETED->value)
            ->when(data_get($this->filters, 'created_at.created_from'), fn($q, $from) =>
            $q->whereDate('created_at', '>=', $from))
            ->when(data_get($this->filters, 'created_at.created_until'), fn($q, $to) =>
            $q->whereDate('created_at', '<=', $to));

        $orderStats = $query->clone()->get(['total', 'profit']);

        $totalProfit = $orderStats->sum('profit');
        $countOrders = $orderStats->count();

        return [
            Stat::make('Orders', $countOrders)
                ->icon('heroicon-o-shopping-bag')
                ->description('Total orders'),

            Stat::make('Profit', 'Rp ' . number_format($totalProfit, 0, ',', '.'))
                ->icon('heroicon-o-banknotes')
                ->description('Total profit'),

            Stat::make(
                'Membership',
                Member::query()->when(
                    $this->filters['start_date'] && $this->filters['end_date'],
                    fn($query) => $query->whereHas('orders', fn($query) => $query->whereBetween('created_at', [$createdFrom, $createdTo]))
                )->count()
            )
                ->icon('heroicon-o-user-group')
                ->description('Total Member'),
        ];
    }
}
