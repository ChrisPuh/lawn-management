<?php

declare(strict_types=1);

use App\Contracts\LawnCare\DeleteLawnCareActionContract;
use App\Models\LawnCare;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('DeleteLawnCareAction', function (): void {
    beforeEach(function (): void {
        $this->lawnCare = LawnCare::factory()->create();
        $this->user = User::factory()->create();
        $this->deleteLawnCare = app(DeleteLawnCareActionContract::class);

    });

    it('deletes lawn care', function (): void {

        expect(LawnCare::count())->toBe(1);

        $this->deleteLawnCare->execute($this->lawnCare, $this->user->id);

        expect(LawnCare::count())->toBe(0);

    });

    it('logs the deletion', function (): void {

        $this->deleteLawnCare->execute($this->lawnCare, $this->user->id);

        $this->assertDatabaseHas('lawn_care_logs', [
            'lawn_care_id' => $this->lawnCare->id,
            'action' => 'deleted',
            'user_id' => $this->user->id,
            'data' => json_encode([
                'type' => $this->lawnCare->type->value,
                'care_data' => $this->lawnCare->care_data,
                'deleted_at' => now()->toDateTimeString(),
            ]),
        ]);

    });

});
