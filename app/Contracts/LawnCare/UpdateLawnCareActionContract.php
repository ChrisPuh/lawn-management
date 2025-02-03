<?php

declare(strict_types=1);

namespace App\Contracts\LawnCare;

use App\DataObjects\LawnCare\BaseLawnCareData;
use App\DataObjects\LawnCare\UpdateFertilizingData;
use App\DataObjects\LawnCare\UpdateMowingData;
use App\DataObjects\LawnCare\UpdateWateringData;
use App\Enums\LawnCare\LawnCareType;
use App\Models\LawnCare;

interface UpdateLawnCareActionContract
{
    /**
     * Führt eine Update-Aktion für eine LawnCare mit den angegebenen Parametern aus.
     *
     * @param LawnCare $lawnCare Die zu aktualisierende Lawn Care Instanz
     * @param LawnCareType $type Der Typ der Rasenpflege
     * @param UpdateWateringData|UpdateMowingData|UpdateFertilizingData $data Die Aktualisierungsdaten
     *
     * @return LawnCare
     */
    public function execute(LawnCare $lawnCare, LawnCareType $type, BaseLawnCareData $data): LawnCare;
}
