<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Planned = 'u_najavi';
    case InProgress = 'u_izgradnji';
    case Completed = 'zavrseno';

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'U najavi',
            self::InProgress => 'U izgradnji',
            self::Completed => 'Završeno',
        };
    }
}
