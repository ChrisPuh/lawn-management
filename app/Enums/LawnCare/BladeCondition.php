<?php

declare(strict_types=1);

namespace App\Enums\LawnCare;

enum BladeCondition: string
{
    case SHARP = 'sharp';
    case DULL = 'dull';
    case WORN = 'worn';
    case NEW = 'new';

    public function label(): string
    {
        return match ($this) {
            self::SHARP => 'Scharf',
            self::DULL => 'Stumpf',
            self::WORN => 'Abgenutzt',
            self::NEW => 'Neu',
        };
    }
}
