<?php

declare(strict_types=1);

namespace App\DataObjects\LawnCare;

use App\Contracts\LawnCare\CareData;
use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\MowingPattern;
use InvalidArgumentException;

final readonly class MowingData implements CareData
{
    public function __construct(
        public float $height_mm,
        public ?MowingPattern $pattern = null,
        public bool $collected = true,
        public ?BladeCondition $blade_condition = null,
        public ?int $duration_minutes = null,
    ) {
        if ($duration_minutes !== null && $duration_minutes <= 0) {
            throw new InvalidArgumentException('Duration minutes must be positive');
        }
    }

    /**
     * @param array{
     *     height_mm: float|string|int,
     *     pattern?: string|null,
     *     collected?: bool|string|int|null,
     *     blade_condition?: string|null,
     *     duration_minutes?: int|string|null
     * } $data
     */
    public static function from(array $data): self
    {
        return new self(
            height_mm: (float) $data['height_mm'],
            pattern: isset($data['pattern']) ? MowingPattern::tryFrom($data['pattern']) : null,
            collected: (bool) ($data['collected'] ?? true),
            blade_condition: isset($data['blade_condition']) ? BladeCondition::tryFrom($data['blade_condition']) : null,
            duration_minutes: isset($data['duration_minutes']) ? (int) $data['duration_minutes'] : null,
        );
    }

    /**
     * @return array{
     *     height_mm: float,
     *     pattern: string|null,
     *     collected: bool,
     *     blade_condition: string|null,
     *     duration_minutes: int|null
     * }
     */
    public function toArray(): array
    {
        return [
            'height_mm' => $this->height_mm,
            'pattern' => $this->pattern?->value,
            'collected' => $this->collected,
            'blade_condition' => $this->blade_condition?->value,
            'duration_minutes' => $this->duration_minutes,
        ];
    }
}
