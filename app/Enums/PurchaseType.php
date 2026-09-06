<?php

namespace App\Enums;

enum PurchaseType: string
{
    case Modal = 'modal';
    case Pemeliharaan = 'pemeliharaan';

    public function code(): string
    {
        return match ($this) {
            self::Modal => '53',
            self::Pemeliharaan => '52',
        };
    }
}
