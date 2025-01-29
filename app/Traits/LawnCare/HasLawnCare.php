<?php

declare(strict_types=1);

namespace App\Traits\LawnCare;

use App\Enums\LawnCare\LawnCareType;

/**
 * @property-read array{type: string, date: string}|null $latest_care
 */
trait HasLawnCare
{
    public function getLatestCare(?string $format = 'd.m.Y'): ?array
    {
        $latestCare = $this->lawnCares()
            ->whereNotNull('performed_at')
            ->orderByDesc('performed_at')
            ->first();

        if (! $latestCare) {
            return null;
        }

        return [
            'type' => $this->getTypeLabel($latestCare->type),
            'date' => $latestCare->performed_at->format($format),
        ];
    }

    private function getTypeLabel(LawnCareType $type): string
    {
        return match ($type) {
            LawnCareType::MOW => 'gemäht',
            LawnCareType::FERTILIZE => 'gedüngt',
            LawnCareType::WATER => 'gewässert',
            default => $type->value,
        };
    }
}
