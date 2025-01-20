<?php

declare(strict_types=1);

namespace App\DataObjects\LawnCare;

use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;
use DateMalformedStringException;
use DateTime;
use InvalidArgumentException;

final readonly class CreateWateringData extends BaseLawnCareData
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
     * @throws DateMalformedStringException
     *
     * //
     *
     */
    public static function fromArray(array $validatedData, int $userId): self
    {
        return new self(
            lawn_id: $validatedData['lawn_id'],
            user_id: $userId,
            amount_liters: (float) $validatedData['amount_liters'],
            duration_minutes: (int) $validatedData['duration_minutes'],
            method: WateringMethod::from($validatedData['method']),
            temperature_celsius: $validatedData['temperature_celsius'],
            weather_condition: $validatedData['weather_condition'],
            time_of_day: $validatedData['time_of_day'],
            notes: $validatedData['notes'],
            performed_at: isset($validatedData['performed_at'])
                ? new DateTime($validatedData['performed_at'])
                : null,
            scheduled_for: isset($validatedData['scheduled_for'])
                ? new DateTime($validatedData['scheduled_for'])
                : null
        );

    }
}
