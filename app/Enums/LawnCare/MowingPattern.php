<?php

namespace App\Enums\LawnCare;

enum MowingPattern: string
{
    case DIAGONAL = 'diagonal';
    case STRIPES = 'stripes';
    case CHECKERBOARD = 'checkerboard';

    public function label(): string
    {
        return match ($this) {
            self::DIAGONAL => 'Diagonal',
            self::STRIPES => 'Streifen',
            self::CHECKERBOARD => 'Schachbrett',
        };
    }

}
