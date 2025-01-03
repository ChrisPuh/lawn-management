<?php

declare(strict_types=1);

namespace App\Enums;

enum GrassType
{
    case Sport;
    case Garden;
    case Park;

    public function label(): string
    {
        return match ($this) {
            self::Sport => 'Sportrasen',
            self::Garden => 'Gartenrasen',
            self::Park => 'Parkrasen',
        };
    }

    public function value(): string
    {
        return match ($this) {
            self::Sport => 'Sport',
            self::Garden => 'Garden',
            self::Park => 'Park',
        };
    }
}
