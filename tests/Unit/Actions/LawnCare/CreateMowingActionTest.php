<?php

declare(strict_types=1);

use App\Actions\LawnCare\CreateMowingAction;
use App\Contracts\LawnCare\LogLawnCareActionContract;
use App\DataObjects\LawnCare\CreateMowingData;
use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\LawnCareType;
use App\Enums\LawnCare\MowingPattern;
use App\Models\Lawn;
use App\Models\LawnCare;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Constants to avoid magic numbers in tests
const DEFAULT_HEIGHT_MM = 45.5;
const DEFAULT_COLLECTED = true;

beforeEach(function () {
    $this->action = new CreateMowingAction(
        app(LogLawnCareActionContract::class)
    );
    $this->user = User::factory()->create();
    $this->lawn = Lawn::factory()->create([
        'user_id' => $this->user->id,
    ]);
});

/**
 * Helper to generate CreateMowingData for tests
 */
function generateCreateMowingData(
    int $lawnId,
    int $userId,
    float $heightMm = DEFAULT_HEIGHT_MM,
    bool $collected = DEFAULT_COLLECTED,
    ?MowingPattern $pattern = null,
    ?BladeCondition $bladeCondition = null,
    ?int $durationMinutes = null,
    ?string $notes = null,
    ?DateTime $performedAt = null,
    ?DateTime $scheduledFor = null
): CreateMowingData {
    return new CreateMowingData(
        lawn_id: $lawnId,
        user_id: $userId,
        height_mm: $heightMm,
        pattern: $pattern,
        collected: $collected,
        blade_condition: $bladeCondition,
        duration_minutes: $durationMinutes,
        notes: $notes,
        performed_at: $performedAt,
        scheduled_for: $scheduledFor
    );
}

describe('CreateMowingAction', function () {
    describe('creation', function () {
        test('with minimal data', function () {
            // Arrange
            $mowingData = generateCreateMowingData(
                lawnId: $this->lawn->id,
                userId: $this->user->id
            );

            // Act
            $lawnCare = $this->action->execute($mowingData);

            // Assert
            expect($lawnCare)
                ->toBeInstanceOf(LawnCare::class)
                ->and($lawnCare->lawn_id)->toBe($this->lawn->id)
                ->and($lawnCare->created_by_id)->toBe($this->user->id)
                ->and($lawnCare->type)->toBe(LawnCareType::MOW)
                ->and($lawnCare->performed_at)->not->toBeNull()
                ->and($lawnCare->scheduled_for)->toBeNull();

            $careData = $lawnCare->getCareData();
            expect($careData)
                ->height_mm->toBe(DEFAULT_HEIGHT_MM)
                ->collected->toBeTrue()
                ->pattern->toBeNull()
                ->blade_condition->toBeNull()
                ->duration_minutes->toBeNull();
        });

        test('with full data', function () {
            // Arrange
            $performedAt = new DateTime();
            $scheduledFor = new DateTime('+1 day');
            $mowingData = generateCreateMowingData(
                lawnId: $this->lawn->id,
                userId: $this->user->id,
                heightMm: 50.0,
                collected: true,
                pattern: MowingPattern::DIAGONAL,
                bladeCondition: BladeCondition::SHARP,
                durationMinutes: 45,
                notes: 'Test mowing notes',
                performedAt: $performedAt,
                scheduledFor: $scheduledFor
            );

            // Act
            $lawnCare = $this->action->execute($mowingData);

            // Assert
            expect($lawnCare)
                ->toBeInstanceOf(LawnCare::class)
                ->and($lawnCare->lawn_id)->toBe($this->lawn->id)
                ->and($lawnCare->created_by_id)->toBe($this->user->id)
                ->and($lawnCare->type)->toBe(LawnCareType::MOW)
                ->and($lawnCare->notes)->toBe('Test mowing notes')
                ->and($lawnCare->performed_at->format('Y-m-d H:i:s'))->toBe($performedAt->format('Y-m-d H:i:s'))
                ->and($lawnCare->scheduled_for->format('Y-m-d H:i:s'))->toBe($scheduledFor->format('Y-m-d H:i:s'));

            $careData = $lawnCare->getCareData();
            expect($careData)
                ->height_mm->toBe(50.0)
                ->pattern->toBe(MowingPattern::DIAGONAL)
                ->collected->toBeTrue()
                ->blade_condition->toBe(BladeCondition::SHARP)
                ->duration_minutes->toBe(45);
        });
    });

    describe('timing handling', function () {
        test('uses current time if performed_at is not provided', function () {
            // Arrange
            $mowingData = generateCreateMowingData(
                lawnId: $this->lawn->id,
                userId: $this->user->id
            );

            $before = now();

            // Act
            $lawnCare = $this->action->execute($mowingData);

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
            $mowingData = generateCreateMowingData(
                lawnId: $this->lawn->id,
                userId: $this->user->id
            );

            // Act
            $lawnCare = $this->action->execute($mowingData);

            // Assert
            expect(LawnCare::query()->count())->toBe(1);
            $this->assertDatabaseHas('lawn_care_logs', [
                'lawn_care_id' => $lawnCare->id,
                'user_id' => $mowingData->user_id,
                'action' => 'created',
            ]);
        });
    });
});
