<?php

declare(strict_types=1);

namespace App\Actions\LawnCare;

use App\DataObjects\LawnCare\UpdateWateringData;
use App\DataObjects\LawnCare\WateringData;
use App\Enums\LawnCare\LawnCareType;
use App\Models\LawnCare;

final readonly class UpdateWateringAction
{
    public function __construct(
        private LogLawnCareAction $logLawnCare,
    ) {}

    public function execute(LawnCare $lawnCare, UpdateWateringData $data): LawnCare
    {
        $lawnCare->update([
            'lawn_id' => $data->lawn_id,
            'created_by_id' => $data->user_id,
            'type' => LawnCareType::WATER,
            'care_data' => new WateringData(
                amount_liters: $data->amount_liters,
                duration_minutes: $data->duration_minutes,
                method: $data->method,
                temperature_celsius: $data->temperature_celsius,
                weather_condition: $data->weather_condition,
                time_of_day: $data->time_of_day,
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
