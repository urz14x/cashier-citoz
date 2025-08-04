<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank_transfer';

    public function getLabel(): ?string
    {
        return str($this->value)->title();
    }
    public function getColor(): string
    {
        return match ($this) {
            self::CASH => 'success',
            self::BANK_TRANSFER => 'warning',
        };
}
}
