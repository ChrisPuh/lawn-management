<?php

declare(strict_types=1);

use App\Actions\LawnCare\CreateWateringAction;
use App\Contracts\LawnCare\CreateLawnCareActionContract;
use App\Contracts\LawnCare\LogLawnCareActionContract;
use App\DataObjects\LawnCare\CreateWateringData;
use App\Enums\LawnCare\LawnCareType;
use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;
use App\Models\Lawn;
use App\Models\LawnCare;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Constants to avoid magic numbers in tests
const DEFAULT_AMOUNT_LITERS = 10.5;
const DEFAULT_DURATION_MINUTES = 30;

beforeEach(function () {
    $this->action = new CreateWateringAction(
        app(LogLawnCareActionContract::class)
    );
    $this->user = User::factory()->create();
    $this->lawn = Lawn::factory()->create([
        'user_id' => $this->user->id,
    ]);
});

/**
 * Helper to generate CreateWateringData for tests
 */
function generateCreateWateringData(
    int $lawnId,
    int $userId,
    float $amountLiters = DEFAULT_AMOUNT_LITERS,
    int $durationMinutes = DEFAULT_DURATION_MINUTES,
    WateringMethod $method = WateringMethod::MANUAL,
    ?float $temperatureCelsius = null,
    ?WeatherCondition $weatherCondition = null,
    ?TimeOfDay $timeOfDay = null,
    ?string $notes = null,
    ?DateTime $performedAt = null,
    ?DateTime $scheduledFor = null
): CreateWateringData {
    return new CreateWateringData(
        lawn_id: $lawnId,
        user_id: $userId,
        amount_liters: $amountLiters,
        duration_minutes: $durationMinutes,
        method: $method,
        temperature_celsius: $temperatureCelsius,
        weather_condition: $weatherCondition,
        time_of_day: $timeOfDay,
        notes: $notes,
        performed_at: $performedAt,
        scheduled_for: $scheduledFor
    );
}

describe('CreateWateringAction', function () {
    describe('creation', function () {
        test('with minimal data', function () {
            //            $lawnCareAction = app(CreateLawnCareActionContract::class);
            //            $lawnCare = $lawnCareAction->execute(type: LawnCareType::WATER, data: CreateWateringData::fromArray(
            //                validatedData: [
            //                    'lawn_id' => $this->lawn->id,
            //                    'amount_liters' => DEFAULT_AMOUNT_LITERS,
            //                    'duration_minutes' => DEFAULT_DURATION_MINUTES,
            //                    'method' => WateringMethod::MANUAL->value,
            //                    'temperature_celsius' => null,
            //                    'weather_condition' => null,
            //                    'time_of_day' => null,
            //                    'notes' => null,
            //                    'performed_at' => now()->format('Y-m-d H:i:s') ,
            //                    'scheduled_for' => null,
            //                ],
            //                userId: $this->user->id,
            //            ));
            //            dd($lawnCare);

            // Arrange
            $wateringData = generateCreateWateringData(
                lawnId: $this->lawn->id,
                userId: $this->user->id
            );

            // Act
            $lawnCare = $this->action->execute($wateringData);

            // Assert
            expect($lawnCare)
                ->toBeInstanceOf(LawnCare::class)
                ->and($lawnCare->lawn_id)->toBe($this->lawn->id)
                ->and($lawnCare->created_by_id)->toBe($this->user->id)
                ->and($lawnCare->type)->toBe(LawnCareType::WATER)
                ->and($lawnCare->performed_at)->not->toBeNull()
                ->and($lawnCare->scheduled_for)->toBeNull();

            $careData = $lawnCare->getCareData();
            expect($careData)
                ->amount_liters->toBe(DEFAULT_AMOUNT_LITERS)
                ->duration_minutes->toBe(DEFAULT_DURATION_MINUTES)
                ->method->toBe(WateringMethod::MANUAL)
                ->temperature_celsius->toBeNull()
                ->weather_condition->toBeNull()
                ->time_of_day->toBeNull();
        });

        test('with full data', function () {
            // Arrange
            $performedAt = new DateTime;
            $scheduledFor = new DateTime('+1 day');
            $wateringData = generateCreateWateringData(
                lawnId: $this->lawn->id,
                userId: $this->user->id,
                temperatureCelsius: 22.5,
                weatherCondition: WeatherCondition::SUNNY,
                timeOfDay: TimeOfDay::MORNING,
                notes: 'Test notes',
                performedAt: $performedAt,
                scheduledFor: $scheduledFor
            );

            // Act
            $lawnCare = $this->action->execute($wateringData);

            // Assert
            expect($lawnCare)
                ->toBeInstanceOf(LawnCare::class)
                ->and($lawnCare->lawn_id)->toBe($this->lawn->id)
                ->and($lawnCare->created_by_id)->toBe($this->user->id)
                ->and($lawnCare->type)->toBe(LawnCareType::WATER)
                ->and($lawnCare->notes)->toBe('Test notes')
                ->and($lawnCare->performed_at->format('Y-m-d H:i:s'))->toBe($performedAt->format('Y-m-d H:i:s'))
                ->and($lawnCare->scheduled_for->format('Y-m-d H:i:s'))->toBe($scheduledFor->format('Y-m-d H:i:s'));
        });
    });

    describe('timing handling', function () {
        test('uses current time if performed_at is not provided', function () {
            // Arrange
            $wateringData = generateCreateWateringData(
                lawnId: $this->lawn->id,
                userId: $this->user->id
            );

            $before = now();

            // Act
            $lawnCare = $this->action->execute($wateringData);

            $after = now();

            // Assert
            expect($lawnCare->performed_at)
                ->toBeInstanceOf(DateTime::class)
                ->and($lawnCare->performed_at->timestamp)
                ->toBeGreaterThanOrEqual($before->timestamp)
                ->toBeLessThanOrEqual($after->timestamp);
        });
    });

    describe('logging', function () {
        test('creates log entry', function () {
            // Arrange
            $wateringData = generateCreateWateringData(
                lawnId: $this->lawn->id,
                userId: $this->user->id
            );

            // Act
            $lawnCare = $this->action->execute($wateringData);

            // Assert
            expect(LawnCare::query()->count())->toBe(1);
            $this->assertDatabaseHas('lawn_care_logs', [
                'lawn_care_id' => $lawnCare->id,
                'user_id' => $wateringData->user_id,
                'action' => 'created',
            ]);
        });
    });
});
