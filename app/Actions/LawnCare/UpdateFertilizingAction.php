<?php

declare(strict_types=1);

namespace App\Actions\LawnCare;

use App\DataObjects\LawnCare\FertilizingData;
use App\DataObjects\LawnCare\UpdateFertilizingData;
use App\Enums\LawnCare\LawnCareType;
use App\Models\LawnCare;

final readonly class UpdateFertilizingAction
{
    public function __construct(
        private LogLawnCareAction $logLawnCare,
    ) {}

    public function execute(LawnCare $lawnCare, UpdateFertilizingData $data): LawnCare
    {
        $lawnCare->update([
            'lawn_id' => $data->lawn_id,
            'created_by_id' => $data->user_id,
            'type' => LawnCareType::FERTILIZE,
            'care_data' => new FertilizingData(
                product_name: $data->product_name,
                amount_per_sqm: $data->amount_per_sqm,
                nutrients: $data->nutrients,
                watered: $data->watered,
                temperature_celsius: $data->temperature_celsius,
                weather_condition: $data->weather_condition,
            ),
            'notes' => $data->notes,
            'performed_at' => $data->performed_at ?? now(),
            'scheduled_for' => $data->scheduled_for,
        ]);

        $this->logLawnCare->execute(
            lawn_care: $lawnCare,
            action: 'updated',
            user_id: $data->user_id,
        );

        return $lawnCare;
    }
}
