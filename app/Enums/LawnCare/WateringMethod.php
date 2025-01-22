<?php

declare(strict_types=1);

namespace App\Enums\LawnCare;

enum WateringMethod: string
{
    case SPRINKLER = 'sprinkler';
    case MANUAL = 'manual';
    case IRRIGATION = 'irrigation';
}
