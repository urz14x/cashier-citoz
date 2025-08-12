<?php

namespace App\Filament\Resources\MemberAttendanceResource\Pages;

use App\Filament\Resources\MemberAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMemberAttendance extends EditRecord
{
    protected static string $resource = MemberAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
