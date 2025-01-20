<?php

declare(strict_types=1);

// tests/Unit/Models/LawnCareTest.php

use App\DataObjects\LawnCare\FertilizingData;
use App\DataObjects\LawnCare\MowingData;
use App\DataObjects\LawnCare\WateringData;
use App\Enums\LawnCare\LawnCareType;
use App\Enums\LawnCare\MowingPattern;
use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;
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
            $completed = LawnCare::factory()->completed()->create();
            $regular = LawnCare::factory()->create();

            $scheduledCares = LawnCare::scheduled()->get();

            expect($scheduledCares)
                ->toHaveCount(1)
                ->first()->id->toBe($scheduled->id);
        });

        it('can scope to completed tasks', function (): void {
            $completed = LawnCare::factory()->completed()->create();
            $scheduled = LawnCare::factory()->scheduled()->create();

            $completedCares = LawnCare::completed()->get();

            expect($completedCares)
                ->toHaveCount(1)
                ->first()->id->toBe($completed->id);
        });

        it('can scope to specific lawn', function (): void {
            $lawn = Lawn::factory()->create();
            $lawnCare = LawnCare::factory()->for($lawn)->create();
            $otherCare = LawnCare::factory()->create();

            $lawnCares = LawnCare::forLawn($lawn)->get();

            expect($lawnCares)
                ->toHaveCount(1)
                ->first()->id->toBe($lawnCare->id);
        });
    });

    describe('care data handling', function (): void {
        it('handles mowing data correctly', function (): void {
            $lawnCare = LawnCare::factory()
                ->mowing(45.5)
                ->create();

            $careData = $lawnCare->getCareData();

            expect($careData)
                ->toBeInstanceOf(MowingData::class)
                ->and($careData->height_mm)->toBe(45.5)
                ->and($careData->pattern)->toBeInstanceOf(MowingPattern::class);

            // Test array conversion
            $arrayData = $careData->toArray();
            expect($arrayData)
                ->toHaveKey('height_mm')
                ->toHaveKey('pattern')
                ->toHaveKey('collected');
        });

        it('handles watering data correctly', function (): void {
            $lawnCare = LawnCare::factory()
                ->watering()
                ->create();

            $careData = $lawnCare->getCareData();

            expect($careData)
                ->toBeInstanceOf(WateringData::class)
                ->and($careData->amount_liters)->toBeFloat()
                ->and($careData->duration_minutes)->toBeInt()
                ->and($careData->method)->toBeInstanceOf(WateringMethod::class);

            // Optional fields should either be null or correct instances
            if ($careData->weather_condition !== null) {
                expect($careData->weather_condition)->toBeInstanceOf(WeatherCondition::class);
            }

            if ($careData->time_of_day !== null) {
                expect($careData->time_of_day)->toBeInstanceOf(TimeOfDay::class);
            }
        });

        it('handles fertilizing data correctly', function (): void {
            $lawnCare = LawnCare::factory()
                ->fertilizing()
                ->create();

            $careData = $lawnCare->getCareData();

            expect($careData)
                ->toBeInstanceOf(FertilizingData::class)
                ->and($careData->product_name)->toBeString()
                ->and($careData->amount_per_sqm)->toBeFloat()
                ->and($careData->nutrients)->toBeArray();
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

            expect($lawnCare->fresh()->getCareData())
                ->toBeInstanceOf(MowingData::class)
                ->and($lawnCare->fresh()->getCareData()->height_mm)
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
