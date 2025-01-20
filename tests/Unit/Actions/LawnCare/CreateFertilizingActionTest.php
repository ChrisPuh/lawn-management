<?php

declare(strict_types=1);

use App\Actions\LawnCare\CreateFertilizingAction;
use App\Contracts\LawnCare\LogLawnCareActionContract;
use App\DataObjects\LawnCare\CreateFertilizingData;
use App\Enums\LawnCare\LawnCareType;
use App\Enums\LawnCare\WeatherCondition;
use App\Models\Lawn;
use App\Models\LawnCare;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Constants to avoid magic numbers in tests
const DEFAULT_AMOUNT_PER_SQM = 0.5;
const DEFAULT_NUTRIENTS = ['N', 'P', 'K'];

beforeEach(function (): void {
    $this->action = new CreateFertilizingAction(
        app(LogLawnCareActionContract::class)
    );

    /** @var User $this->user */
    $this->user = User::factory()->create();
    $this->lawn = Lawn::factory()->create([
        'user_id' => $this->user->id,
    ]);
});

/**
 * Helper to generate CreateFertilizingData for tests
 */
function generateCreateFertilizingData(
    int $lawnId,
    int $userId,
    string $productName = 'Test Fertilizer',
    float $amountPerSqm = DEFAULT_AMOUNT_PER_SQM,
    array $nutrients = DEFAULT_NUTRIENTS,
    bool $watered = false,
    ?float $temperatureCelsius = null,
    ?WeatherCondition $weatherCondition = null,
    ?string $notes = null,
    ?DateTime $performedAt = null,
    ?DateTime $scheduledFor = null
): CreateFertilizingData {
    return new CreateFertilizingData(
        lawn_id: $lawnId,
        user_id: $userId,
        product_name: $productName,
        amount_per_sqm: $amountPerSqm,
        nutrients: $nutrients,
        watered: $watered,
        temperature_celsius: $temperatureCelsius,
        weather_condition: $weatherCondition,
        notes: $notes,
        performed_at: $performedAt,
        scheduled_for: $scheduledFor
    );
}

describe('CreateFertilizingAction', function (): void {
    describe('creation', function (): void {
        test('with minimal data', function (): void {
            // Arrange
            $fertilizingData = generateCreateFertilizingData(
                lawnId: $this->lawn->id,
                userId: $this->user->id
            );

            // Act
            $lawnCare = $this->action->execute($fertilizingData);

            // Assert
            expect($lawnCare)
                ->toBeInstanceOf(LawnCare::class)
                ->and($lawnCare->lawn_id)->toBe($this->lawn->id)
                ->and($lawnCare->created_by_id)->toBe($this->user->id)
                ->and($lawnCare->type)->toBe(LawnCareType::FERTILIZE)
                ->and($lawnCare->performed_at)->not->toBeNull()
                ->and($lawnCare->scheduled_for)->toBeNull();

            $careData = $lawnCare->getCareData();
            expect($careData)
                ->product_name->toBe('Test Fertilizer')
                ->amount_per_sqm->toBe(DEFAULT_AMOUNT_PER_SQM)
                ->nutrients->toBe(DEFAULT_NUTRIENTS)
                ->watered->toBeFalse()
                ->temperature_celsius->toBeNull()
                ->weather_condition->toBeNull();
        });

        test('with full data', function (): void {
            // Arrange
            $performedAt = new DateTime;
            $scheduledFor = new DateTime('+1 day');
            $fertilizingData = generateCreateFertilizingData(
                lawnId: $this->lawn->id,
                userId: $this->user->id,
                productName: 'Advanced Fertilizer',
                amountPerSqm: 0.75,
                nutrients: ['N', 'P', 'K', 'Ca'],
                watered: true,
                temperatureCelsius: 22.5,
                weatherCondition: WeatherCondition::SUNNY,
                notes: 'Test fertilizing notes',
                performedAt: $performedAt,
                scheduledFor: $scheduledFor
            );

            // Act
            $lawnCare = $this->action->execute($fertilizingData);

            // Assert
            expect($lawnCare)
                ->toBeInstanceOf(LawnCare::class)
                ->and($lawnCare->lawn_id)->toBe($this->lawn->id)
                ->and($lawnCare->created_by_id)->toBe($this->user->id)
                ->and($lawnCare->type)->toBe(LawnCareType::FERTILIZE)
                ->and($lawnCare->notes)->toBe('Test fertilizing notes')
                ->and($lawnCare->performed_at->format('Y-m-d H:i:s'))->toBe($performedAt->format('Y-m-d H:i:s'))
                ->and($lawnCare->scheduled_for->format('Y-m-d H:i:s'))->toBe($scheduledFor->format('Y-m-d H:i:s'));

            $careData = $lawnCare->getCareData();
            expect($careData)
                ->product_name->toBe('Advanced Fertilizer')
                ->amount_per_sqm->toBe(0.75)
                ->nutrients->toBe(['N', 'P', 'K', 'Ca'])
                ->watered->toBeTrue()
                ->temperature_celsius->toBe(22.5)
                ->weather_condition->toBe(WeatherCondition::SUNNY);
        });
    });

    describe('timing handling', function (): void {
        test('uses current time if performed_at is not provided', function (): void {
            // Arrange
            $fertilizingData = generateCreateFertilizingData(
                lawnId: $this->lawn->id,
                userId: $this->user->id
            );

            $before = now();

            // Act
            $lawnCare = $this->action->execute($fertilizingData);

            $after = now();

            // Assert
            expect($lawnCare->performed_at)
                ->toBeInstanceOf(DateTime::class)
                ->and($lawnCare->performed_at->timestamp)
                ->toBeGreaterThanOrEqual($before->timestamp)
                ->toBeLessThanOrEqual($after->timestamp);
        });
    });

    describe('logging', function (): void {
        test('creates log entry', function (): void {
            // Arrange
            $fertilizingData = generateCreateFertilizingData(
                lawnId: $this->lawn->id,
                userId: $this->user->id
            );

            // Act
            $lawnCare = $this->action->execute($fertilizingData);

            // Assert
            expect(LawnCare::query()->count())->toBe(1);
            $this->assertDatabaseHas('lawn_care_logs', [
                'lawn_care_id' => $lawnCare->id,
                'user_id' => $fertilizingData->user_id,
                'action' => 'created',
            ]);
        });
    });
});
