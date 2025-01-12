<?php

declare(strict_types=1);

namespace App\Enums;

enum LawnImageType: string
{
    case BEFORE = 'before';
    case AFTER = 'after';
    case GENERAL = 'general';

    public function label(): string
    {
        return match ($this) {
            self::BEFORE => 'Vorher',
            self::AFTER => 'Nachher',
            self::GENERAL => 'General'
        };
    }
}
