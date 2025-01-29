<?php

declare(strict_types=1);

namespace App\DataObjects;

use InvalidArgumentException;

final readonly class PositiveInteger
{
    private function __construct(
        private int $value
    ) {
        if ($value <= 0) {
            throw new InvalidArgumentException('Value must be positive');
        }
    }

    public static function from(int|string $value): self
    {
        return new self((int) $value);
    }

    public static function tryFrom(int|string|null $value): ?self
    {
        if ($value === null) {
            return null;
        }

        try {
            return self::from($value);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    public function value(): int
    {
        return $this->value;
    }
}
