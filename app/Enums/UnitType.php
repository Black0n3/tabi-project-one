<?php

namespace App\Enums;

enum UnitType: string
{
    case Stan = 'stan';
    case Kuca = 'kuca';

    public function label(): string
    {
        return match ($this) {
            self::Stan => 'Stan',
            self::Kuca => 'Kuća',
        };
    }
}
