<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PositionEmployee: string implements HasLabel
{
    case MANAGER = 'manager';
    case CASHIER = 'cashier';
    case SOCIAL_MEDIA = 'social_media';
    case CLEANING_STAFF = 'cleaning_staff';

    public function getLabel(): ?string
    {
        return match ($this) {
             self::MANAGER => 'Manager',
             self::CASHIER => 'Cashier',
             self::SOCIAL_MEDIA => 'Sosial Media',
             self::CLEANING_STAFF => 'Cleaning staff'
        };
    }
}
