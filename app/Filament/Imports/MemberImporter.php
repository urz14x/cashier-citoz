<?php

namespace App\Filament\Imports;

use App\Enums\Gender;
use App\Enums\MemberStatus;
use App\Models\Member;
use App\Models\Package;
use App\Models\PersonalTrainer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;


class MemberImporter extends Importer
{
    protected static ?string $model = Member::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('package_id')
                ->label('Package')
                ->requiredMapping()
                ->rules(['required', 'exists:packages,id']),
            ImportColumn::make('personal_trainers')
                ->rules(['nullable', 'string']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'string']),
            ImportColumn::make('social_media')
                ->rules(['nullable', 'string']),
            ImportColumn::make('phone')
                ->rules(['nullable', 'string']),
            ImportColumn::make('address')
                ->rules(['nullable', 'string']),
            ImportColumn::make('gender')
                ->rules(['required', 'in:M,F']),
            ImportColumn::make('joined')
                ->rules(['required', 'date']),
            ImportColumn::make('expired')
                ->rules(['nullable', 'date']),
            ImportColumn::make('status')
                ->rules(['nullable', 'in:active,extend,expired']),
        ];
    }

    public function resolveRecord(): ?Member
    {
        $package = Package::where('name', $this->data['package_name'])->first();

        if (!$package) {
            $this->failValidation('package_name', 'Package not found.');
            return null;
        }

        return new Member([
            'package_id' => $package->id,
            'name' => $this->data['name'],
            'social_media' => $this->data['social_media'] ?? '',
            'phone' => $this->data['phone'] ?? '',
            'address' => $this->data['address'] ?? '',
            'gender' => $this->data['gender'],
            'joined' => $this->data['joined'],
            'expired' => $this->data['expired'] ?? null,
            'status' => $this->data['status'] ?? MemberStatus::ACTIVE,
        ]);
    }

    public function afterSave(): void
    {
        // Tangani personal_trainers many-to-many
        if (!empty($this->data['personal_trainers'])) {
            $names = collect(explode(',', $this->data['personal_trainers']))->map(fn($n) => trim($n));
            $trainers = PersonalTrainer::whereIn('name', $names)->pluck('id');
            $this->record->personalTrainers()->sync($trainers);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import member selesai. ' . number_format($import->successful_rows) . ' baris berhasil diimpor.';

        if ($failedRows = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRows) . ' baris gagal diimpor.';
        }

        return $body;
    }
}
