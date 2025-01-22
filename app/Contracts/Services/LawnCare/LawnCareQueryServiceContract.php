<?php

namespace App\Contracts\Services\LawnCare;

use App\Models\Lawn;
use App\Models\LawnCare;
use Illuminate\Database\Eloquent\Collection;

interface LawnCareQueryServiceContract
{
    /**
     * @return Collection<int, LawnCare>
     */
    public function getFilteredLawnCares(Lawn $lawn, ?string $type = null): Collection;
}
