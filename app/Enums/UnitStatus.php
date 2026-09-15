<?php

namespace App\Enums;

enum UnitStatus: string
{
    case Dostupno = 'dostupno';
    case Rezervirano = 'rezervirano';
    case Prodano = 'prodano';

    public function label(): string
    {
        return match ($this) {
            self::Dostupno => 'Dostupno',
            self::Rezervirano => 'Rezervirano',
            self::Prodano => 'Prodano',
        };
    }
}
