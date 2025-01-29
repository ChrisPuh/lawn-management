<?php

declare(strict_types=1);

namespace App\Enums\LawnCare;

enum MowingPattern: string
{
    case STRIPE = 'stripe';
    case DIAGONAL = 'diagonal';
    case CHECKERBOARD = 'checkerboard';
    case SPIRAL = 'spiral';

    public function label(): string
    {
        return match ($this) {
            self::STRIPE => 'Streifen',
            self::DIAGONAL => 'Diagonal',
            self::CHECKERBOARD => 'Schachbrett',
            self::SPIRAL => 'Spirale',
        };
    }
}
