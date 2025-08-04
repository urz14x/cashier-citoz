<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    // Simpan PT dari form untuk digunakan di afterCreate
    protected array $ptPivotData = [];

    protected function getRedirectUrl(): string
    {
        return route('filament.app.resources.orders.create-transaction', [
            'record' => $this->record->order_number,
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Simpan data repeater ke properti
        $this->ptPivotData = $data['personalTrainers'] ?? [];

        // Hitung total dari PT
        $trainerTotal = collect($this->ptPivotData)->sum(function ($item) {
            $pt = \App\Models\PersonalTrainer::find($item['personal_trainer_id'] ?? null);
            if (!$pt) return 0;

            return ($item['pt_type'] ?? 'per_visit') === 'per_bulan'
                ? $pt->price_per_month
                : $pt->price_per_visit;
        });

        // Hitung total akhir
        $baseTotal = intval($data['base_total'] ?? 0);
        $discount = intval($data['discount'] ?? 0);
        $data['total'] = max(0, $baseTotal + $trainerTotal - $discount);

        // Jangan simpan ke kolom yang tidak ada
        unset($data['personalTrainers']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $pivotData = collect($this->ptPivotData)
            ->filter(fn($item) => !empty($item['personal_trainer_id']))
            ->mapWithKeys(function ($item) {
                return [
                    $item['personal_trainer_id'] => [
                        'pt_type' => $item['pt_type'] ?? 'per_visit',
                    ],
                ];
            })->toArray();

        // Simpan ke pivot Order ↔ PT
        if (!empty($pivotData)) {
            $this->record->personalTrainers()->sync($pivotData);
        }

        // Simpan ke Member ↔ PT (tanpa pt_type)
        if (!empty($pivotData) && $this->record->member) {
            $this->record->member->personalTrainers()->syncWithoutDetaching(array_keys($pivotData));
        }

        // Recalculate total (optional)
        $this->record->calculateTotal();
    }
}
