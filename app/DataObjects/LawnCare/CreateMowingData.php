<?php

declare(strict_types=1);

namespace App\DataObjects\LawnCare;

use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\MowingPattern;
use App\Http\Requests\BaseLawnCareRequest;
use App\Http\Requests\CreateMowingRequest;
use DateMalformedStringException;
use DateTime;

final readonly class CreateMowingData extends BaseLawnCareData
{
    public function __construct(
        int $lawn_id,
        int $user_id,
        public float $height_mm,
        public ?MowingPattern $pattern = null,
        public bool $collected = true,
        public ?BladeCondition $blade_condition = null,
        public ?int $duration_minutes = null,
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
        assert($request instanceof CreateMowingRequest);

        return new self(
            lawn_id: $request->validated('lawn_id'),
            user_id: $userId,
            height_mm: (float) $request->validated('height_mm'),
            pattern: $request->validated('pattern')
                ? MowingPattern::from($request->validated('pattern'))
                : null,
            collected: $request->validated('collected', true),
            blade_condition: $request->validated('blade_condition')
                ? BladeCondition::from($request->validated('blade_condition'))
                : null,
            duration_minutes: $request->validated('duration_minutes'),
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
