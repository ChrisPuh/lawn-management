<?php

declare(strict_types=1);

namespace App\DataObjects\LawnCare;

use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\MowingPattern;
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
     * @param array{
     *  lawn_id:int,
     *  height_mm: float,
     *  pattern:MowingPattern::class,
     *  collected:boolean,
     *  blade_condition:BladeCondition::class,
     *  duration_minutes:int,
     *  notes:sting,
     *  performed_at:DateTime::class,
     *  scheduled_for:DateTime::class
     *} $validatedData
     *
     * @throws DateMalformedStringException
     */
    public static function fromArray(array $validatedData, int $userId): self
    {
        return new self(
            lawn_id: $validatedData['lawn_id'],
            user_id: $userId,
            height_mm: (float) $validatedData['height_mm'],
            pattern: isset($validatedData['pattern']) ? MowingPattern::tryFrom($validatedData['pattern']) : null,
            collected: (bool) ($validatedData['collected'] ?? true),
            blade_condition: isset($validatedData['blade_condition'])
                ? BladeCondition::tryFrom($validatedData['blade_condition'])
                : null,
            duration_minutes: isset($validatedData['duration_minutes'])
                ? (int) $validatedData['duration_minutes']
                : null,
            notes: $validatedData['notes'] ?? null,
            performed_at: isset($validatedData['performed_at']) ? new DateTime($validatedData['performed_at']) : null,
            scheduled_for: isset($validatedData['scheduled_for']) ? new DateTime($validatedData['scheduled_for']) : null,
        );
    }
}
