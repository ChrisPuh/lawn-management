<?php

declare(strict_types=1);

namespace App\Enums\LawnCare;

enum WeatherCondition: string
{
    case SUNNY = 'sunny';
    case CLOUDY = 'cloudy';
    case OVERCAST = 'overcast';
    case RAINY = 'rainy';
}
