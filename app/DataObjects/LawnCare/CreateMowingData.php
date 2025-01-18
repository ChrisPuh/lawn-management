<?php

namespace App\DataObjects\LawnCare;

use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\MowingPattern;
use App\Http\Requests\CreateMowingRequest;
use DateTime;

final readonly class CreateMowingData
{
    public function __construct(
        public int $lawn_id,
        public int $user_id,
        public float $height_mm,
        public ?MowingPattern $pattern = null,
        public bool $collected = true,
        public ?BladeCondition $blade_condition = null,
        public ?int $duration_minutes = null,
        public ?string $notes = null,
        public ?DateTime $performed_at = null,
        public ?DateTime $scheduled_for = null,
    ) {}

    public static function fromRequest(CreateMowingRequest $request, int $userId): self
    {
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
