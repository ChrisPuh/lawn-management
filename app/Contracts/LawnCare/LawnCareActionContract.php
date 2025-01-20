<?php

declare(strict_types=1);

namespace App\Contracts\LawnCare;

use App\DataObjects\LawnCare\BaseLawnCareData;
use App\Models\LawnCare;

interface LawnCareActionContract
{
    /**
     * Führt die jeweilige Rasenpflege-Aktion aus.
     *
     * @param  BaseLawnCareData  $data  Die benötigten Parameter
     */
    public function execute(BaseLawnCareData $data): LawnCare;
}
