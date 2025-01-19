<?php

declare(strict_types=1);

namespace App\DataObjects\LawnCare;

use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;
use App\Http\Requests\BaseLawnCareRequest;
use App\Http\Requests\CreateWateringRequest;
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
     */
    public static function fromRequest(BaseLawnCareRequest $request, int $userId): self
    {
        assert($request instanceof CreateWateringRequest);

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
