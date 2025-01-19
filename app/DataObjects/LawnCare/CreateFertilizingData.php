<?php

declare(strict_types=1);

namespace App\DataObjects\LawnCare;

use App\Enums\LawnCare\WeatherCondition;
use App\Http\Requests\BaseLawnCareRequest;
use App\Http\Requests\CreateFertilizingRequest;
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
    public static function fromRequest(BaseLawnCareRequest $request, int $userId): self
    {
        assert($request instanceof CreateFertilizingRequest);

        return new self(
            lawn_id: $request->validated('lawn_id'),
            user_id: $userId,
            product_name: $request->validated('product_name'),
            amount_per_sqm: (float) $request->validated('amount_per_sqm'),
            nutrients: $request->validated('nutrients'),
            watered: $request->validated('watered', false),
            temperature_celsius: $request->validated('temperature_celsius'),
            weather_condition: $request->validated('weather_condition'),
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
