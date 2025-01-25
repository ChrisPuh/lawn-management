<?php

declare(strict_types=1);

use App\Contracts\Services\LawnCare\LawnCareQueryServiceContract;
use App\Enums\LawnCare\LawnCareType;
use App\Models\Lawn;
use App\Models\LawnCare;
use App\Models\User;
use Illuminate\Support\Carbon;

describe('LawnCareQueryService', tests: function (): void {
    beforeEach(closure: function (): void {
        /** @var User $user */
        $this->user = User::factory()->createOne();
        $this->lawn = Lawn::factory()->createOne([
            'user_id' => $this->user->id,
        ]);
        $this->service = app(LawnCareQueryServiceContract::class);
    });

    it('filters lawn cares by type', function (): void {
        // Arrange
        LawnCare::factory()
            ->mowing()
            ->createOne([
                'lawn_id' => $this->lawn->id,
                'created_by_id' => $this->user->id,
            ]);

        LawnCare::factory()
            ->fertilizing()
            ->createOne([
                'lawn_id' => $this->lawn->id,
                'created_by_id' => $this->user->id,
            ]);

        // Act
        $result = $this->service->getFilteredLawnCares(
            lawn: $this->lawn,
            type: LawnCareType::MOW->value
        );

        // Assert
        expect($result)
            ->toHaveCount(1)
            ->first()->type->toBe(LawnCareType::MOW);
    });

    it('orders by performed_at and created_at in descending order', function (): void {
        // Arrange
        Carbon::setTestNow('2024-01-01 12:00:00');

        $oldest = LawnCare::factory()
            ->mowing()
            ->createOne([
                'lawn_id' => $this->lawn->id,
                'created_by_id' => $this->user->id,
                'performed_at' => now()->subDays(2),
                'created_at' => now()->subDays(2),
            ]);

        $middle = LawnCare::factory()
            ->mowing()
            ->createOne([
                'lawn_id' => $this->lawn->id,
                'created_by_id' => $this->user->id,
                'performed_at' => now()->subDay(),
                'created_at' => now()->subDay(),
            ]);

        $newest = LawnCare::factory()
            ->mowing()
            ->createOne([
                'lawn_id' => $this->lawn->id,
                'created_by_id' => $this->user->id,
                'performed_at' => now(),
                'created_at' => now(),
            ]);

        // Act
        $result = $this->service->getFilteredLawnCares($this->lawn);

        // Assert
        expect($result->pluck('id')->all())
            ->toBe([$newest->id, $middle->id, $oldest->id]);
    });

    it('loads createdBy relation', function (): void {
        // Arrange
        LawnCare::factory()
            ->mowing()
            ->createOne([
                'lawn_id' => $this->lawn->id,
                'created_by_id' => $this->user->id,
            ]);

        // Act
        $result = $this->service->getFilteredLawnCares($this->lawn);

        // Assert
        expect($result->first()->relationLoaded('createdBy'))->toBeTrue();
    });

    it('returns empty collection when no lawn cares exist', function (): void {
        // Act
        $result = $this->service->getFilteredLawnCares($this->lawn);

        // Assert
        expect($result)
            ->toBeCollection()
            ->toBeEmpty();
    });
});
