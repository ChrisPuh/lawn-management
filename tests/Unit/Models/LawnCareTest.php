<?php

declare(strict_types=1);

// tests/Unit/Models/LawnCareTest.php

use App\DataObjects\LawnCare\FertilizingData;
use App\DataObjects\LawnCare\MowingData;
use App\DataObjects\LawnCare\WateringData;
use App\Enums\LawnCare\LawnCareType;
use App\Enums\LawnCare\MowingPattern;
use App\Models\Lawn;
use App\Models\LawnCare;
use App\Models\User;

describe('LawnCare Model', function (): void {
    beforeEach(function (): void {
        // Setup for each test
    });

    describe('attributes', function (): void {
        it('has fillable fields', function (): void {
            $lawnCare = new LawnCare;

            expect($lawnCare->getFillable())->toContain(
                'lawn_id',
                'type',
                'care_data',
                'notes',
                'performed_at',
                'scheduled_for',
                'completed_at',
                'created_by_id'
            );
        });

        it('casts attributes correctly', function (): void {
            $lawnCare = new LawnCare;

            expect($lawnCare->getCasts())
                ->toHaveKey('type', LawnCareType::class)
                ->toHaveKey('care_data', 'array')
                ->toHaveKey('performed_at', 'datetime')
                ->toHaveKey('scheduled_for', 'datetime')
                ->toHaveKey('completed_at', 'datetime');
        });
    });

    describe('relationships', function (): void {
        it('belongs to a lawn', function (): void {
            $lawnCare = LawnCare::factory()->create();

            expect($lawnCare->lawn)
                ->toBeInstanceOf(Lawn::class)
                ->and($lawnCare->lawn_id)
                ->toBe($lawnCare->lawn->id);
        });

        it('belongs to a creator', function (): void {
            $lawnCare = LawnCare::factory()->create();

            expect($lawnCare->createdBy)
                ->toBeInstanceOf(User::class)
                ->and($lawnCare->created_by_id)
                ->toBe($lawnCare->createdBy->id);
        });
    });

    describe('scopes', function (): void {
        it('can scope to scheduled tasks', function (): void {
            $scheduled = LawnCare::factory()->scheduled()->create();
            LawnCare::factory()->completed()->create();
            LawnCare::factory()->create();

            $scheduledCares = LawnCare::scheduled()->get();

            expect($scheduledCares->count())->toBe(1)
                ->and($scheduledCares->first()->id)->toBe($scheduled->id);
        });

        it('can scope to completed tasks', function (): void {
            $completed = LawnCare::factory()->completed()->create();
            LawnCare::factory()->scheduled()->create();

            $completedCares = LawnCare::completed()->get();

            expect($completedCares->count())->toBe(1)
                ->and($completedCares->first()->id)->toBe($completed->id);
        });

        it('can scope to specific lawn', function (): void {
            $lawn = Lawn::factory()->create();
            $lawnCare = LawnCare::factory()->for($lawn)->create();
            LawnCare::factory()->create();

            $lawnCares = LawnCare::forLawn($lawn)->get();

            expect($lawnCares->count())->toBe(1)
                ->and($lawnCares->first()->id)->toBe($lawnCare->id);
        });
    });

    describe('care data handling', function (): void {
        it('handles mowing data correctly', function (): void {
            $lawnCare = LawnCare::factory()
                ->mowing(45.5)
                ->create();

            $careData = $lawnCare->getCareData();

            expect($careData)
                ->toBeInstanceOf(MowingData::class);

            $arrayData = $careData->toArray();
            expect($arrayData)
                ->toHaveKeys(['height_mm', 'pattern', 'collected'])
                ->and($arrayData['height_mm'])->toBe(45.5);
        });

        it('handles watering data correctly', function (): void {
            $lawnCare = LawnCare::factory()
                ->watering()
                ->create();

            $careData = $lawnCare->getCareData();

            expect($careData)
                ->toBeInstanceOf(WateringData::class);

            $arrayData = $careData->toArray();
            expect($arrayData)
                ->toHaveKeys(['amount_liters', 'duration_minutes', 'method'])
                ->and($arrayData['amount_liters'])->toBeFloat()
                ->and($arrayData['duration_minutes'])->toBeInt();
        });

        it('handles fertilizing data correctly', function (): void {
            $lawnCare = LawnCare::factory()
                ->fertilizing()
                ->create();

            $careData = $lawnCare->getCareData();

            expect($careData)
                ->toBeInstanceOf(FertilizingData::class);

            $arrayData = $careData->toArray();
            expect($arrayData)
                ->toHaveKeys(['product_name', 'amount_per_sqm', 'nutrients'])
                ->and($arrayData['product_name'])->toBeString()
                ->and($arrayData['amount_per_sqm'])->toBeFloat()
                ->and($arrayData['nutrients'])->toBeArray();
        });

        it('can update care data', function (): void {
            $lawnCare = LawnCare::factory()
                ->mowing(40.0)
                ->create();

            $newData = new MowingData(
                height_mm: 50.0,
                pattern: MowingPattern::DIAGONAL,
                collected: true,
            );

            $lawnCare->setCareData($newData);
            $lawnCare->save();

            $updatedCareData = $lawnCare->fresh()->getCareData();
            expect($updatedCareData)
                ->toBeInstanceOf(MowingData::class)
                ->and($updatedCareData->toArray()['height_mm'])
                ->toBe(50.0);
        });
    });

    describe('task management', function (): void {
        it('can complete a task', function (): void {
            $lawnCare = LawnCare::factory()->scheduled()->create();

            expect($lawnCare->completed_at)->toBeNull();

            $lawnCare->complete();

            expect($lawnCare->fresh()->completed_at)
                ->not->toBeNull();
        });

        it('can determine if task is scheduled', function (): void {
            $scheduled = LawnCare::factory()->scheduled()->create();
            $regular = LawnCare::factory()->create();

            expect($scheduled->isScheduled())->toBeTrue()
                ->and($regular->isScheduled())->toBeFalse();
        });

        it('can determine if task is completed', function (): void {
            $completed = LawnCare::factory()->completed()->create();
            $regular = LawnCare::factory()->create();

            expect($completed->isCompleted())->toBeTrue()
                ->and($regular->isCompleted())->toBeFalse();
        });
    });
});
