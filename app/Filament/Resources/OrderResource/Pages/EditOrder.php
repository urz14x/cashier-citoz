<?php
namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Member;
use App\Models\PersonalTrainer;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $ptItems = $data['personalTrainers'] ?? [];

        // Hitung total harga dari semua PT
        $trainerPrice = collect($ptItems)->sum(function ($item) {
            $pt = PersonalTrainer::find($item['personal_trainer_id'] ?? null);
            if (!$pt) return 0;

            return ($item['pt_type'] ?? 'per_visit') === 'per_bulan'
                ? intval(str_replace(',', '', $pt->price_per_month))
                : intval(str_replace(',', '', $pt->price_per_visit));
        });

        // Konversi angka
        $data['base_total'] = intval(str_replace(',', '', $data['base_total'] ?? 0));
        $data['discount'] = intval(str_replace(',', '', $data['discount'] ?? 0));

        // Hitung ulang total
        $data['total'] = max(0, $data['base_total'] + $trainerPrice - $data['discount']);

        // Hapus field repeater agar tidak disimpan ke kolom order (bukan kolom DB)
        unset($data['personalTrainers']);

        return $data;
    }

    protected function afterSave(): void
    {
        $pts = $this->form->getState()['personalTrainers'] ?? [];

        // Filter hanya yang valid
        $pivotData = collect($pts)
            ->filter(fn ($item) => !empty($item['personal_trainer_id']))
            ->mapWithKeys(function ($item) {
                return [
                    $item['personal_trainer_id'] => [
                        'pt_type' => $item['pt_type'] ?? 'per_visit',
                    ],
                ];
            })->toArray();

        // Sync ke order ↔ PT
        $this->record->personalTrainers()->sync($pivotData);

        // Sync juga ke member ↔ PT (jika ada)
        if (!empty($pivotData) && $this->record->member) {
            $this->record->member->personalTrainers()->syncWithoutDetaching(array_keys($pivotData));
        }

        // Hitung ulang total
        $this->record->calculateTotal();
    }
}
