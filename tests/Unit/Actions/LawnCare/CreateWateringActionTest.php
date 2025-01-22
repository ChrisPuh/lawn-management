<?php

declare(strict_types=1);

use App\Actions\LawnCare\CreateWateringAction;
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

describe('CreateWateringAction', function (): void {
    // Use closures to create local variables
    beforeEach(function (): void {
        $logActionContract = app(LogLawnCareActionContract::class);
        $this->action = fn () => new CreateWateringAction($logActionContract);

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

    describe('creation', function (): void {
        test('with minimal data', function (): void {
            // Arrange
            $action = ($this->action)();
            $wateringData = generateCreateWateringData(
                lawnId: $this->lawn->id,
                userId: $this->user->id
            );

            // Act
            $lawnCare = $action->execute($wateringData);

            // Assert
            expect($lawnCare)
                ->toBeInstanceOf(LawnCare::class)
                ->lawn_id->toBe($this->lawn->id)
                ->created_by_id->toBe($this->user->id)
                ->type->toBe(LawnCareType::WATER)
                ->performed_at->not->toBeNull()
                ->scheduled_for->toBeNull();

            $careData = $lawnCare->getCareData();
            $careArray = $careData->toArray();
            expect($careArray)
                ->toHaveKey('amount_liters', DEFAULT_AMOUNT_LITERS)
                ->toHaveKey('duration_minutes', DEFAULT_DURATION_MINUTES)
                ->toHaveKey('method', WateringMethod::MANUAL->value)
                ->toHaveKey('temperature_celsius', null)
                ->toHaveKey('weather_condition', null)
                ->toHaveKey('time_of_day', null);
        });

        test('with full data', function (): void {
            // Arrange
            $action = ($this->action)();
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
            $lawnCare = $action->execute($wateringData);

            // Assert
            expect($lawnCare)
                ->toBeInstanceOf(LawnCare::class)
                ->lawn_id->toBe($this->lawn->id)
                ->created_by_id->toBe($this->user->id)
                ->type->toBe(LawnCareType::WATER)
                ->notes->toBe('Test notes')
                ->performed_at->format('Y-m-d H:i:s')->toBe($performedAt->format('Y-m-d H:i:s'))
                ->scheduled_for->format('Y-m-d H:i:s')->toBe($scheduledFor->format('Y-m-d H:i:s'));
        });
    });

    describe('timing handling', function (): void {
        test('uses current time if performed_at is not provided', function (): void {
            // Arrange
            $action = ($this->action)();
            $wateringData = generateCreateWateringData(
                lawnId: $this->lawn->id,
                userId: $this->user->id
            );

            $before = now();

            // Act
            $lawnCare = $action->execute($wateringData);

            $after = now();

            // Assert
            expect($lawnCare->performed_at)
                ->toBeInstanceOf(DateTime::class)
                ->timestamp->toBeGreaterThanOrEqual($before->timestamp)
                ->toBeLessThanOrEqual($after->timestamp);
        });
    });

    describe('logging', function (): void {
        test('creates log entry', function (): void {
            // Arrange
            $action = ($this->action)();
            $wateringData = generateCreateWateringData(
                lawnId: $this->lawn->id,
                userId: $this->user->id
            );

            // Act
            $lawnCare = $action->execute($wateringData);

            // Assert
            expect(LawnCare::query()->count())->toBe(1);

            // Use Laravel's database assertion
            $this->assertDatabaseHas('lawn_care_logs', [
                'lawn_care_id' => $lawnCare->id,
                'user_id' => $wateringData->user_id,
                'action' => 'created',
            ]);
        });
    });
});
