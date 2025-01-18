<?php

declare(strict_types=1);

namespace App\Contracts\Lawn\LawnCare;

interface LawnCareData
{
    /**
     * @return array{
     *     height_mm?: float,
     *     pattern?: string|null,
     *     collected?: bool,
     *     blade_condition?: string|null,
     *     duration_minutes?: positive-int|null,
     *     product_name?: string,
     *     amount_per_sqm?: float,
     *     nutrient_ratio?: string,
     *     watered?: bool,
     *     temperature_celsius?: float|null,
     *     weather_condition?: string|null,
     *     amount_liters?: float,
     *     method?: string,
     *     time_of_day?: string|null
     * }
     */
    public function toArray(): array;

}
