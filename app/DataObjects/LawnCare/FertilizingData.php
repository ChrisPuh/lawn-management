<?php

declare(strict_types=1);

namespace App\DataObjects\LawnCare;

use App\Contracts\LawnCare\CareData;
use App\Enums\LawnCare\WeatherCondition;

final class FertilizingData implements CareData
{
    /**
     * @param array{
     *    nutrient_n: float,
     *    nutrient_p: float,
     *    nutrient_k: float
     * } $nutrients
     */
    public function __construct(
        public string $product_name,
        public float $amount_per_sqm,
        public array $nutrients,
        public bool $watered = false,
        public ?float $temperature_celsius = null,
        public ?WeatherCondition $weather_condition = null,
    ) {}

    /**
     * @param array{
     *     product_name: string,
     *     amount_per_sqm: float|string|int,
     *     nutrients: array{nutrient_n: float, nutrient_p: float, nutrient_k: float},
     *     watered?: bool|string|int|null,
     *     temperature_celsius?: float|string|null,
     *     weather_condition?: string|null
     * } $data
     */
    public static function from(array $data): self
    {
        return new self(
            product_name: $data['product_name'],
            amount_per_sqm: (float) $data['amount_per_sqm'],
            nutrients: $data['nutrients'],
            watered: (bool) ($data['watered'] ?? false),
            temperature_celsius: isset($data['temperature_celsius']) ? (float) $data['temperature_celsius'] : null,
            weather_condition: isset($data['weather_condition']) ? WeatherCondition::tryFrom($data['weather_condition']) : null,
        );
    }

    /**
     * @return array{
     *     product_name: string,
     *     amount_per_sqm: float,
     *     nutrients: array{nutrient_n: float, nutrient_p: float, nutrient_k: float},
     *     watered: bool,
     *     temperature_celsius: float|null,
     *     weather_condition: string|null
     * }
     */
    public function toArray(): array
    {
        return [
            'product_name' => $this->product_name,
            'amount_per_sqm' => $this->amount_per_sqm,
            'nutrients' => $this->nutrients,
            'watered' => $this->watered,
            'temperature_celsius' => $this->temperature_celsius,
            'weather_condition' => $this->weather_condition?->value,
        ];
    }
}
