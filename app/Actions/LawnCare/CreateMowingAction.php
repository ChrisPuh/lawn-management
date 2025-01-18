<?php

namespace App\Actions\LawnCare;

use App\DataObjects\LawnCare\CreateMowingData;
use App\DataObjects\LawnCare\MowingData;
use App\Enums\LawnCare\LawnCareType;
use App\Models\LawnCare;

final readonly class CreateMowingAction
{
    public function __construct(
        private LogLawnCareAction $logLawnCare,
    ) {}
    public function execute(CreateMowingData $data): LawnCare
    {
        $lawnCare = LawnCare::query()->create([
            'lawn_id' => $data->lawn_id,
            'created_by_id' => $data->user_id,
            'type' => LawnCareType::MOW,
            'care_data' => new MowingData(
                height_mm: $data->height_mm,
                pattern: $data->pattern,
                collected: $data->collected,
                blade_condition: $data->blade_condition,
                duration_minutes: $data->duration_minutes,
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
