<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CategoryPurchaseStats extends BaseWidget
{
    protected static string $view = 'filament.widgets.category-purchase-stats';
    protected int | string | array $columnSpan = 'full';
    public $categoryStats = [];

    public function mount(): void
    {
        $categories = [
            'Makanan & Minuman',
            'Operasional-GYM',
            'Operasional-Senam',
            'ATK',
            'Maintenance-GYM',
            'Maintenance-Senam',
            'Lain-lain'
        ];
        $this->categoryStats = collect($categories)->map(function ($name) {
            $total = \App\Models\Category::where('name', $name)
                ->with('purchases')
                ->first()
                ?->purchases
                ->sum('price') ?? 0;

            return [
                'name' => $name,
                'total' => $total,
            ];
        });
    }
}
