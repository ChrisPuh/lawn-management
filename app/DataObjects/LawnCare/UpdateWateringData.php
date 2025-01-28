<?php

declare(strict_types=1);

namespace App\DataObjects\LawnCare;

use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;
use DateMalformedStringException;
use DateTime;
use InvalidArgumentException;

final readonly class UpdateWateringData extends BaseLawnCareData
{
    public function __construct(
        int $lawn_id,
        int $user_id,

        public float $amount_liters,
        public int $duration_minutes,
        public WateringMethod $method,
        public ?float $temperature_celsius = null,
        public ?WeatherCondition $weather_condition = null,
        public ?TimeOfDay $time_of_day = null,

        ?string $notes = null,
        ?DateTime $performed_at = null,
        ?DateTime $scheduled_for = null,
    ) {
        if ($amount_liters <= 0) {
            throw new InvalidArgumentException('Amount must be positive');
        }

        if ($duration_minutes <= 0) {
            throw new InvalidArgumentException('Duration must be positive');
        }
        parent::__construct($lawn_id, $user_id, $notes, $performed_at, $scheduled_for);
    }

    /**
     * Create an UpdateWateringData instance
     *
     * @param array{
     *     lawn_id: int,
     *     care_data: array{
     *         amount_liters: float,
     *         duration_minutes: int,
     *         method: string,
     *         temperature_celsius?: float|null,
     *         weather_condition?: string|null,
     *         time_of_day?: string|null
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
            amount_liters: (float) $validatedData['care_data']['amount_liters'],
            duration_minutes: (int) $validatedData['care_data']['duration_minutes'],
            method: WateringMethod::from($validatedData['care_data']['method']),
            temperature_celsius: $validatedData['care_data']['temperature_celsius'] ?? null,
            weather_condition: isset($validatedData['care_data']['weather_condition'])
                ? WeatherCondition::tryFrom($validatedData['care_data']['weather_condition'])
                : null,
            time_of_day: isset($validatedData['care_data']['time_of_day'])
                ? TimeOfDay::tryFrom($validatedData['care_data']['time_of_day'])
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
