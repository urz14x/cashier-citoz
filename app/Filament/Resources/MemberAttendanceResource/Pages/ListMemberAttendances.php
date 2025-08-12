<?php

namespace App\Filament\Resources\MemberAttendanceResource\Pages;

use App\Filament\Resources\MemberAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMemberAttendances extends ListRecords
{
    protected static string $resource = MemberAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
