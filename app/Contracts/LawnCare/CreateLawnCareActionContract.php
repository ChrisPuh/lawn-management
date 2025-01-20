<?php

declare(strict_types=1);

namespace App\Contracts\LawnCare;

use App\DataObjects\LawnCare\BaseLawnCareData;
use App\Enums\LawnCare\LawnCareType;
use App\Models\LawnCare;

interface CreateLawnCareActionContract
{
    /**
     * Führt eine LawnCare-Aktion mit den angegebenen Parametern aus.
     *
     * @param  LawnCareType  $type  Der Typ der Rasenpflege.
     */
    public function execute(LawnCareType $type, BaseLawnCareData $data): LawnCare;
}
