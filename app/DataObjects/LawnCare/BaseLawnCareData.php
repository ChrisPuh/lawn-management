<?php

declare(strict_types=1);

namespace App\DataObjects\LawnCare;

use DateTime;

abstract readonly class BaseLawnCareData
{
    public function __construct(
        public int $lawn_id,
        public int $user_id,
        public ?string $notes = null,
        public ?DateTime $performed_at = null,
        public ?DateTime $scheduled_for = null,
    ) {}

    abstract public static function fromArray(array $validatedData, int $userId): self;
}
