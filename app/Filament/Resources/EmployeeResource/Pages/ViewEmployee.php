<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\EmployeeResource;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getContent(): string
    {
        $qrData = $this->record->qr_code ?? $this->record->id;

        $qr = base64_encode(
            QrCode::format('png')->size(200)->generate($qrData)
        );

        return <<<HTML
            <div class="flex flex-col items-center justify-center py-6">
                <h2 class="text-lg font-bold mb-4">QR Code Pegawai</h2>
                <img src="data:image/png;base64,{$qr}" alt="QR Code Pegawai" class="w-48 h-48 border border-gray-300 rounded-md shadow" />
                <p class="mt-2 text-center font-semibold">{$this->record->name}</p>
            </div>
        HTML;
    }
}
