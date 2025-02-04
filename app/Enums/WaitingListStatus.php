<?php

declare(strict_types=1);

namespace App\Enums;

enum WaitingListStatus: string
{
    case Pending = 'pending';
    case Invited = 'invited';
    case Registered = 'registered';
    case Declined = 'declined';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Wartet',
            self::Invited => 'Eingeladen',
            self::Registered => 'Registriert',
            self::Declined => 'Abgelehnt',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Invited => 'info',
            self::Registered => 'success',
            self::Declined => 'error',
        };
    }
}
