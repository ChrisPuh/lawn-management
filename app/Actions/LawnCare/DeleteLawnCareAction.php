<?php

declare(strict_types=1);

namespace App\Actions\LawnCare;

use App\Contracts\LawnCare\DeleteLawnCareActionContract;
use App\Models\LawnCare;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

final readonly class DeleteLawnCareAction implements DeleteLawnCareActionContract
{
    public function __construct(
        private LogLawnCareAction $logLawnCare,
    ) {}

    public function execute(LawnCare $lawnCare, int $userId): bool
    {
        try {
            return DB::transaction(function () use ($lawnCare, $userId): bool {
                // Create deletion log before deleting
                $this->logLawnCare->execute(
                    lawn_care: $lawnCare,
                    action: 'deleted',
                    user_id: $userId,
                    additional_data: [
                        'deleted_at' => now()->toDateTimeString(),
                        'lawn_id' => $lawnCare->lawn_id,
                        'type' => $lawnCare->type->value,
                        'care_data' => $lawnCare->care_data,
                    ],
                );

                if (! $lawnCare->delete()) {
                    return false;
                }

                return true;
            });
        } catch (Throwable $e) {
            throw new RuntimeException(
                message: "Failed to delete lawn care record: {$e->getMessage()}",
                previous: $e,
            );
        }
    }
}
