<?php

namespace App\Filament\Pages;

use App\Filament\Resources\PurchaseResource\Widgets\PurchaseStats;
use App\Filament\Widgets\CategoryPurchaseStats;
use App\Filament\Widgets\OrderStats;
use App\Filament\Widgets\ProfitChart;
use App\Filament\Widgets\StatsDashboard;
use App\Filament\Widgets\StatsOverview;
use App\Models\Member;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $title = 'Dashboard';

    public function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
            CategoryPurchaseStats::class
        ];
    }

    public function getWidgets(): array
    {
        return [
            OrderStats::class,
            ProfitChart::class,
        ];
    }
    public function getTitle(): string
    {
        $user = auth()->user();

        $hour = now()->format('H');
        $greeting = match (true) {
            $hour >= 5 && $hour < 12 => 'Hallo Selamat Pagi ☀️',
            $hour >= 12 && $hour < 15 => 'Hallo Selamat Siang ☀️',
            $hour >= 15 && $hour < 18 => 'Hallo Selamat Sore 🌄',
            default => 'Hallo Selamat Malam 🌙',
        };

        return "$greeting, {$user->name}";
    }


    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->columns(3)
                ->schema([
                    Select::make('member')
                        ->label('Pilih Member')
                        ->options(fn() => Member::query()->pluck('name', 'id')->toArray())
                        ->placeholder('Semua Member')
                        ->searchable()
                        ->preload(),

                    DatePicker::make('start_date')
                        ->label('Dari Tanggal')
                        ->native(false)
                        ->maxDate(fn(Get $get) => $get('end_date') ? now()->min($get('end_date')) : now()),

                    DatePicker::make('end_date')
                        ->label('Sampai Tanggal')
                        ->native(false)
                        ->minDate(fn(Get $get) => $get('start_date') ?: null)
                        ->maxDate(now()),
                ]),
        ]);
    }
}
