<?php

declare(strict_types=1);

namespace App\Contracts\LawnCare;

use App\DataObjects\LawnCare\BaseLawnCareData;
use App\Enums\LawnCare\LawnCareType;
use App\Models\LawnCare;

interface UpdateLawnCareActionContract
{
    /**
     * Führt eine Update-Aktion für eine LawnCare mit den angegebenen Parametern aus.
     *
     * @param  LawnCare  $lawnCare  Die zu aktualisierende Lawn Care Instanz
     * @param  LawnCareType  $type  Der Typ der Rasenpflege
     * @param  BaseLawnCareData  $data  Die Aktualisierungsdaten
     */
    public function execute(LawnCare $lawnCare, LawnCareType $type, BaseLawnCareData $data): LawnCare;
}
