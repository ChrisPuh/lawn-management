<?php

declare(strict_types=1);

namespace App\DataObjects\LawnCare;

use App\Enums\LawnCare\Nutrient;
use App\Enums\LawnCare\WeatherCondition;
use DateMalformedStringException;
use DateTime;

final readonly class UpdateFertilizingData extends BaseLawnCareData
{
    public function __construct(
        int $lawn_id,
        int $user_id,

        public string $product_name,
        public float $amount_per_sqm,
        public array $nutrients,
        public bool $watered = false,
        public ?float $temperature_celsius = null,
        public ?WeatherCondition $weather_condition = null,

        ?string $notes = null,
        ?DateTime $performed_at = null,
        ?DateTime $scheduled_for = null,
    ) {
        parent::__construct($lawn_id, $user_id, $notes, $performed_at, $scheduled_for);
    }

    /**
     * Create an UpdateFertilizingData instance
     *
     * @param array{
     *     lawn_id: int,
     *     care_data: array{
     *         product_name: string,
     *         amount_per_sqm: float,
     *         nutrients: array<Nutrient>,
     *         watered?: bool,
     *         temperature_celsius?: float|null,
     *         weather_condition?: string|null
     *     },
     *     notes?: string|null,
     *     performed_at?: string|null,
     *     scheduled_for?: string|null
     * } $validatedData
     *
     * @throws DateMalformedStringException
     */
    public static function fromArray(array $validatedData, int $userId): self
    {
        return new self(
            lawn_id: $validatedData['lawn_id'],
            user_id: $userId,
            product_name: $validatedData['care_data']['product_name'],
            amount_per_sqm: (float) $validatedData['care_data']['amount_per_sqm'],
            nutrients: $validatedData['care_data']['nutrients'] ?? [],
            watered: (bool) ($validatedData['care_data']['watered'] ?? false),
            temperature_celsius: isset($validatedData['care_data']['temperature_celsius'])
                ? (float) $validatedData['care_data']['temperature_celsius']
                : null,
            weather_condition: isset($validatedData['care_data']['weather_condition'])
                ? WeatherCondition::tryFrom($validatedData['care_data']['weather_condition'])
                : null,
            notes: $validatedData['notes'] ?? null,
            performed_at: $validatedData['performed_at']
                ? new DateTime($validatedData['performed_at'])
                : null,
            scheduled_for: $validatedData['scheduled_for']
                ? new DateTime($validatedData['scheduled_for'])
                : null
        );
    }
}
