<?php

declare(strict_types=1);

namespace App\Services\LawnCare;

use App\Contracts\Services\LawnCare\LawnCareQueryServiceContract;
use App\Models\Lawn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class LawnCareQueryService implements LawnCareQueryServiceContract
{
    /**
     * {@inheritDoc}
     */
    public function getFilteredLawnCares(Lawn $lawn, ?string $type = null): Collection
    {
        return $lawn->lawnCares()
            ->when($type, function (Builder $query, string $careType): void {
                $query->where('type', $careType);
            })
            ->latest('performed_at')
            ->latest('created_at')
            ->with('createdBy')
            ->get();
    }
}
