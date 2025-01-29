<?php

declare(strict_types=1);

namespace App\Enums\LawnCare;

enum Nutrient: string
{
    case NITROGEN = 'N';
    case PHOSPHORUS = 'P';
    case POTASSIUM = 'K';
    case MAGNESIUM = 'Mg';
    case IRON = 'Fe';

    public function label(): string
    {
        return match ($this) {
            self::NITROGEN => 'Stickstoff (N)',
            self::PHOSPHORUS => 'Phosphor (P)',
            self::POTASSIUM => 'Kalium (K)',
            self::MAGNESIUM => 'Magnesium (Mg)',
            self::IRON => 'Eisen (Fe)',
        };
    }
}
