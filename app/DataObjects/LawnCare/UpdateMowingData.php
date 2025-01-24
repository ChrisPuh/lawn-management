<?php

declare(strict_types=1);

namespace App\DataObjects\LawnCare;

use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\MowingPattern;
use DateMalformedStringException;
use DateTime;

final readonly class UpdateMowingData extends BaseLawnCareData
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
     * Create an UpdateMowingData instance
     *
     * @param array{
     *     lawn_id: int,
     *     care_data: array{
     *         height_mm: float,
     *         pattern?: string|null,
     *         collected?: bool,
     *         blade_condition?: string|null,
     *         duration_minutes?: int|null
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

            height_mm: (float) $validatedData['care_data']['height_mm'],
            pattern: isset($validatedData['care_data']['pattern'])
                ? MowingPattern::tryFrom($validatedData['care_data']['pattern'])
                : null,
            collected: (bool) ($validatedData['care_data']['collected'] ?? true),
            blade_condition: isset($validatedData['care_data']['blade_condition'])
                ? BladeCondition::tryFrom($validatedData['care_data']['blade_condition'])
                : null,
            duration_minutes: isset($validatedData['care_data']['duration_minutes'])
                ? (int) $validatedData['care_data']['duration_minutes']
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
