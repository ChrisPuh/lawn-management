<?php

namespace App\Actions\LawnCare;

use App\Contracts\LawnCare\CareData;
use App\DataObjects\LawnCare\BaseLawnCareData;
use App\Models\LawnCare;

abstract class BaseLawnCareAction
{
    public function __construct(
        protected LogLawnCareAction $logLawnCare,
    ) {}

    protected function createLawnCare(
        BaseLawnCareData $data,
        string $type,
        CareData $careData
    ): LawnCare {
        $lawnCare = LawnCare::query()->create([
            'lawn_id' => $data->lawn_id,
            'created_by_id' => $data->user_id,
            'type' => $type,
            'care_data' => $careData,
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
