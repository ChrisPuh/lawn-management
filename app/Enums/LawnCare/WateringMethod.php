<?php

declare(strict_types=1);

namespace App\Enums\LawnCare;

enum WateringMethod: string
{
    case SPRINKLER = 'sprinkler';
    case MANUAL = 'manual';
    case IRRIGATION = 'irrigation';

    case DRIP = 'drip';
    case HOSE = 'hose';

    public function label(): string
    {
        return match ($this) {
            self::SPRINKLER => 'Rasensprenger',
            self::MANUAL => 'Manuell',
            self::IRRIGATION => 'Integration',
            self::DRIP => 'Tröpfchenbewässerung',
            self::HOSE => 'Gartenschlauch',
        };
    }
}
