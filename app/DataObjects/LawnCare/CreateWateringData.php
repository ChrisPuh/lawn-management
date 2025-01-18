<?php

declare(strict_types=1);

namespace App\DataObjects\LawnCare;

use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;
use App\Http\Requests\CreateWateringRequest;
use DateMalformedStringException;
use DateTime;

final class CreateWateringData
{
    public function __construct(
        public int $lawn_id,
        public int $user_id,

        public float $amount_liters,
        public int $duration_minutes,
        public WateringMethod $method,
        public ?float $temperature_celsius = null,
        public ?WeatherCondition $weather_condition = null,
        public ?TimeOfDay $time_of_day = null,

        public ?string $notes = null,
        public ?DateTime $performed_at = null,
        public ?DateTime $scheduled_for = null,
    ) {}

    /**
     * @throws DateMalformedStringException
     */
    public static function fromRequest(CreateWateringRequest $request, int $userId): self
    {
        return new self(
            lawn_id: $request->validated('lawn_id'),
            user_id: $userId,
            amount_liters: (float) $request->validated('amount_liters'),
            duration_minutes: (int) $request->validated('duration_minutes'),
            method: WateringMethod::from($request->validated('method')),
            temperature_celsius: $request->validated('temperature_celsius'),
            weather_condition: $request->validated('weather_condition'),
            time_of_day: $request->validated('time_of_day'),
            notes: $request->validated('notes'),
            performed_at: $request->validated('performed_at')
                ? new DateTime($request->validated('performed_at'))
                : null,
            scheduled_for: $request->validated('scheduled_for')
                ? new DateTime($request->validated('scheduled_for'))
                : null,
        );
    }
}
