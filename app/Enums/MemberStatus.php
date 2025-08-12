<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MemberStatus: string implements HasLabel
{
    case ACTIVE = 'active';
    case EXTEND = 'extend';
    case EXPIRED = 'expired';

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'ACTIVE',
            self::EXTEND => 'EXTEND',
            self::EXPIRED => 'EXPIRED',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::EXTEND => 'warning',
            self::EXPIRED => 'danger',
        };
    }
}
