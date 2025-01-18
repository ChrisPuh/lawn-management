<?php

declare(strict_types=1);

namespace App\Enums\LawnCare;

enum TimeOfDay: string
{
    case MORNING = 'morning';
    case NOON = 'noon';
    case EVENING = 'evening';
    case NIGHT = 'night';

    public function label(): string
    {
        return match ($this) {
            self::MORNING => 'Morgen',
            self::NOON => 'Mittag',
            self::EVENING => 'Abend',
            self::NIGHT => 'Nacht',
        };
    }

    public function isOptimalForWatering(): bool
    {
        return match ($this) {
            self::MORNING, self::EVENING => true,
            default => false,
        };
    }
}
