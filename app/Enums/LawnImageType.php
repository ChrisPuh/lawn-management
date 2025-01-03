<?php

declare(strict_types=1);

namespace App\Enums;

enum LawnImageType: string
{
    case BEFORE = 'before';
    case AFTER = 'after';

    public function label(): string
    {
        return match ($this) {
            self::BEFORE => 'Before',
            self::AFTER => 'After',
        };
    }
}
