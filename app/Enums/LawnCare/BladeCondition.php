<?php

declare(strict_types=1);

namespace App\Enums\LawnCare;

enum BladeCondition: string
{
    case SHARP = 'sharp';
    case MEDIUM = 'medium';
    case DULL = 'dull';
}
