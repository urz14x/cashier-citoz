<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Enums\PositionEmployee;
use App\Filament\Resources\EmployeeResource;
use App\Models\Employee;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTitle(): string
    {
        return "Daftar Pegawai";
    }

    public function getTabs(): array
    {
        $positions = collect([
            'all' => ['label' => 'Semua', 'badgeColor' => 'primary', 'value' => null],
            ...collect(PositionEmployee::cases())->mapWithKeys(function ($position) {
                return [
                    $position->value => [
                        'label' => $position->getLabel(),
                        'badgeColor' => match ($position) {
                            PositionEmployee::MANAGER => 'info',
                            PositionEmployee::CASHIER => 'success',
                            PositionEmployee::SOCIAL_MEDIA => 'warning',
                            PositionEmployee::CLEANING_STAFF => 'danger',
                        },
                        'value' => $position->value,
                    ]
                ];
            }),
        ]);

        return $positions->mapWithKeys(function ($data, $key) {
            $badgeCount = is_null($data['value'])
                ? Employee::count()
                : Employee::where('position', $data['value'])->count();

            return [
                $key => Tab::make($data['label'])
                    ->badge($badgeCount)
                    ->modifyQueryUsing(fn($query) => is_null($data['value']) ? $query : $query->where('position', $data['value']))
                    ->badgeColor($data['badgeColor'])
            ];
        })->toArray();
    }
}
