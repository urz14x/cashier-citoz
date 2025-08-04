<?php

namespace App\Filament\Exports;

use App\Models\Member;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\Enums\ExportFormat;

class MemberExporter extends Exporter
{
    protected static ?string $model = Member::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('package.name')->label('package_name'),
            ExportColumn::make('name')->label('name'),
            ExportColumn::make('social_media')->label('social_media'),
            ExportColumn::make('phone')->label('phone'),
            ExportColumn::make('address')->label('address'),
            ExportColumn::make('gender')
                ->label('gender')
                ->formatStateUsing(fn($state) => $state?->label() ?? 'N/A'),
            ExportColumn::make('joined')->label('joined'),
            ExportColumn::make('expired')->label('expired'),
            ExportColumn::make('status')->label('status')->formatStateUsing(fn($state) => $state?->value ?? 'N/A'),


            // ExportColumn::make('package.name')->label('package_name'),
            // ExportColumn::make('personalTrainer.name')->label('personal_trainer_name'),
            // ExportColumn::make('name')->label('name'),
            // ExportColumn::make('social_media')->label('social_media'),
            // ExportColumn::make('phone')->label('phone'),
            // ExportColumn::make('address')->label('address'),
            // ExportColumn::make('gender')
            // ->label('gender')
            // ->formatStateUsing(fn($state) => $state?->label() ?? 'N/A'),
            // ExportColumn::make('joined')->label('joined'),
            // ExportColumn::make('expired')->label('expired'),
            // ExportColumn::make('status')
            //     ->label('status')
            //     ->formatStateUsing(fn($state) => ucfirst($state ?? 'N/A')),
        ];
    }
    public function resolveRecords(): \Illuminate\Support\Collection
    {
        return Member::with(['package', 'personalTrainer'])
            ->select([
                'id',
                'package_id',
                'personal_trainer_id',
                'name',
                'social_media',
                'phone',
                'address',
                'gender',
                'joined',
                'expired',
                'status',
            ])
            ->get();
    }
    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your member export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    public function getFormats(): array
    {
        return [
            ExportFormat::Xlsx,
            ExportFormat::Csv
        ];
    }
}
