<?php

declare(strict_types=1);

namespace App\Actions\LawnCare;

use App\Contracts\LawnCare\LawnCareActionContract;
use App\Contracts\LawnCare\LogLawnCareActionContract;
use App\DataObjects\LawnCare\CreateWateringData;
use App\DataObjects\LawnCare\WateringData;
use App\Enums\LawnCare\LawnCareType;
use App\Models\LawnCare;

final readonly class CreateWateringAction implements LawnCareActionContract
{
    public function __construct(
        private LogLawnCareActionContract $logLawnCare,
    ) {}

    public function execute(CreateWateringData|\App\DataObjects\LawnCare\BaseLawnCareData $data): LawnCare
    {
        assert($data instanceof CreateWateringData);
        $lawnCare = LawnCare::query()->create([
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
            action: 'created',
            user_id: $data->user_id,
        );

        return $lawnCare;
    }
}
