<?php

declare(strict_types=1);

namespace App\DataObjects\LawnCare;

use App\Enums\LawnCare\WeatherCondition;
use DateMalformedStringException;
use DateTime;

final readonly class CreateFertilizingData extends BaseLawnCareData
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
     * @throws DateMalformedStringException
     */
    public static function fromArray(array $validatedData, int $userId): self
    {

        return new self(
            lawn_id: $validatedData['lawn_id'],
            user_id: $userId,
            product_name: $validatedData['product_name'],
            amount_per_sqm: (float) $validatedData['amount_per_sqm'],
            nutrients: $validatedData['nutrients'],
            watered: $validatedData['watered'],
            temperature_celsius: isset($validatedData['temperature_celsius'])
                ? (float) $validatedData['temperature_celsius']
                : null,
            weather_condition: isset($validatedData['weather_condition'])
                ? WeatherCondition::tryFrom($validatedData['weather_condition'])
                : null,
            notes: $validatedData['notes'] ?? null,
            performed_at: isset($validatedData['performed_at'])
                ? new DateTime($validatedData['performed_at'])
                : null,
            scheduled_for: isset($validatedData['scheduled_for'])
                ? new DateTime($validatedData['scheduled_for'])
                : null,
        );
    }
}
