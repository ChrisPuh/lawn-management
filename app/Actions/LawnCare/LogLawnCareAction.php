<?php

declare(strict_types=1);

namespace App\Actions\LawnCare;

use App\Models\LawnCare;
use App\Models\LawnCareLog;

final readonly class LogLawnCareAction
{
    /**
     * @param  array<string, mixed>  $additional_data
     */
    public function execute(
        LawnCare $lawn_care,
        string $action,
        int $user_id,
        array $additional_data = [],
    ): LawnCareLog {
        $data = [
            'type' => $lawn_care->type->value,
            'care_data' => $lawn_care->care_data,
            ...$additional_data,
        ];

        return LawnCareLog::query()->create([
            'lawn_care_id' => $lawn_care->id,
            'user_id' => $user_id,
            'action' => $action,
            'data' => $data,
        ]);
    }
}
