<?php

declare(strict_types=1);

namespace App\Enums\LawnCare;

enum WeatherCondition: string
{
    case SUNNY = 'sunny';
    case CLOUDY = 'cloudy';
    case RAINY = 'rainy';
    case WINDY = 'windy';
    case OVERCAST = 'overcast';

    public function label(): string
    {
        return match ($this) {
            self::SUNNY => 'Sonnig',
            self::CLOUDY => 'Bewölkt',
            self::RAINY => 'Regnerisch',
            self::WINDY => 'Windig',
            self::OVERCAST => 'Bedeckt',
        };
    }
}
