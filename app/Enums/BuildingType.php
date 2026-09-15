<?php

namespace App\Enums;

enum BuildingType: string
{
    case Zgrada = 'zgrada';
    case UrbanaVila = 'urbana_vila';
    case Kuca = 'kuca';

    public function label(): string
    {
        return match ($this) {
            self::Zgrada => 'Zgrada',
            self::UrbanaVila => 'Urbana vila',
            self::Kuca => 'Kuća',
        };
    }
}
