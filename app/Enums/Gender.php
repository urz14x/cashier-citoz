<?php

namespace App\Enums;

enum Gender: String
{
    case MALE = 'M';
    case FEMALE = 'F';

    public function label(): string
    {
        return match ($this) {
            self::MALE => 'Pria',
            self::FEMALE => 'Wanita',
        };
    }
}
