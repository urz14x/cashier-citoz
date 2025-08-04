<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Filament\Resources\AttendanceResource\Widgets\AttendanceStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;
    public function getHeaderWidgets(): array
    {
        return [
            AttendanceStats::class,
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTitle(): string
    {
        return "Absen Pegawai Citoz Sport Center";
    }
}
