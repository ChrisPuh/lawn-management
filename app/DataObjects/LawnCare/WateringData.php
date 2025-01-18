<?php

declare(strict_types=1);

namespace App\DataObjects\LawnCare;

use App\Contracts\Lawn\LawnCare\LawnCareData;
use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;
use InvalidArgumentException;

// app/DataObjects/WateringData.php
final readonly class WateringData implements LawnCareData
{
    public function __construct(
        public float $amount_liters,
        public int $duration_minutes,
        public WateringMethod $method,
        public ?float $temperature_celsius = null,
        public ?WeatherCondition $weather_condition = null,
        public ?TimeOfDay $time_of_day = null,
    ) {
        if ($duration_minutes <= 0) {
            throw new InvalidArgumentException('Duration must be positive');
        }
    }

    /**
     * @param array{
     *     amount_liters: float|string|int,
     *     duration_minutes: int|string,
     *     method: string,
     *     temperature_celsius?: float|string|null,
     *     weather_condition?: string|null,
     *     time_of_day?: string|null
     * } $data
     *
     * @throws InvalidArgumentException
     */
    public static function from(array $data): self
    {
        return new self(
            amount_liters: (float) $data['amount_liters'],
            duration_minutes: (int) $data['duration_minutes'],
            method: WateringMethod::from($data['method']),
            temperature_celsius: isset($data['temperature_celsius'])
                ? (float) $data['temperature_celsius']
                : null,
            weather_condition: isset($data['weather_condition'])
                ? WeatherCondition::tryFrom($data['weather_condition'])
                : null,
            time_of_day: isset($data['time_of_day'])
                ? TimeOfDay::tryFrom($data['time_of_day'])
                : null,
        );
    }

    /**
     * @return array{
     *     amount_liters: float,
     *     duration_minutes: int,
     *     method: string,
     *     temperature_celsius: float|null,
     *     weather_condition: string|null,
     *     time_of_day: string|null
     * }
     */
    public function toArray(): array
    {
        return [
            'amount_liters' => $this->amount_liters,
            'duration_minutes' => $this->duration_minutes,
            'method' => $this->method->value,
            'temperature_celsius' => $this->temperature_celsius,
            'weather_condition' => $this->weather_condition?->value,
            'time_of_day' => $this->time_of_day?->value,
        ];
    }
}
