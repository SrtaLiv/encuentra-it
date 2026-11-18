<?php

namespace App\Enums;

enum CodingLevel: string
{
    use HasOptions;

    case None = 'none';
    case Basic = 'basic';
    case Core = 'core';

    public function label(): string
    {
        return match ($this) {
            self::None => 'No necesita programar',
            self::Basic => 'Programación básica',
            self::Core => 'Programar es central',
        };
    }
}
