<?php

namespace App\Enums\LawnCare;

enum WateringMethod: string
{
    case SPRINKLER = 'sprinkler';
    case MANUAL = 'manual';
    case IRRIGATION = 'irrigation';
}
