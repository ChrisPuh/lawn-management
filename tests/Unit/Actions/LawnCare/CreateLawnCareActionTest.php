<?php

declare(strict_types=1);

use App\Contracts\LawnCare\CreateLawnCareActionContract;
use App\DataObjects\LawnCare\CreateFertilizingData;
use App\DataObjects\LawnCare\CreateMowingData;
use App\DataObjects\LawnCare\CreateWateringData;
use App\Enums\LawnCare\LawnCareType;
use App\Enums\LawnCare\MowingPattern;
use App\Enums\LawnCare\WateringMethod;
use App\Models\Lawn;
use App\Models\LawnCare;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('CreateLawnCareAction', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
        $this->lawn = Lawn::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Resolve the action from the container to ensure it's using the correct binding
        $this->createLawnCareAction = app(CreateLawnCareActionContract::class);
    });

    describe('successful execution', function (): void {
        test('executes mowing action', function (): void {
            // Arrange
            $mowingData = new CreateMowingData(
                lawn_id: $this->lawn->id,
                user_id: $this->user->id,
                height_mm: 45.5,
                collected: true,
                pattern: MowingPattern::DIAGONAL
            );

            // Act
            $result = $this->createLawnCareAction->execute(
                type: LawnCareType::MOW,
                data: $mowingData
            );

            // Assert
            expect($result)
                ->toBeInstanceOf(LawnCare::class)
                ->lawn_id->toBe($this->lawn->id)
                ->created_by_id->toBe($this->user->id)
                ->type->toBe(LawnCareType::MOW);
        });

        test('executes fertilizing action', function (): void {
            // Arrange
            $fertilizingData = new CreateFertilizingData(
                lawn_id: $this->lawn->id,
                user_id: $this->user->id,
                product_name: 'Test Fertilizer',
                amount_per_sqm: 0.5,
                nutrients: ['N', 'P', 'K'],
                watered: false
            );

            // Act
            $result = $this->createLawnCareAction->execute(
                type: LawnCareType::FERTILIZE,
                data: $fertilizingData
            );

            // Assert
            expect($result)
                ->toBeInstanceOf(LawnCare::class)
                ->lawn_id->toBe($this->lawn->id)
                ->created_by_id->toBe($this->user->id)
                ->type->toBe(LawnCareType::FERTILIZE);
        });

        test('executes watering action', function (): void {
            // Arrange
            $wateringData = new CreateWateringData(
                lawn_id: $this->lawn->id,
                user_id: $this->user->id,
                amount_liters: 10.5,
                duration_minutes: 30,
                method: WateringMethod::MANUAL
            );

            // Act
            $result = $this->createLawnCareAction->execute(
                type: LawnCareType::WATER,
                data: $wateringData
            );

            // Assert
            expect($result)
                ->toBeInstanceOf(LawnCare::class)
                ->lawn_id->toBe($this->lawn->id)
                ->created_by_id->toBe($this->user->id)
                ->type->toBe(LawnCareType::WATER);
        });
    });

    describe('error handling', function (): void {
        test('throws ValueError when attempting to create enum with invalid value', function (): void {
            // Act & Assert
            $this->expectException(ValueError::class);
            $this->expectExceptionMessageMatches('/"LIME" is not a valid backing value for enum/');

            // This will throw a ValueError
            LawnCareType::from('LIME');

        });

        test('throws InvalidArgumentException for unsupported lawn care type', function (): void {
            $action = app(CreateLawnCareActionContract::class);

            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage('Unsupported lawn care action type: lime');

            $action->execute(LawnCareType::LIME, $dummyData = new CreateMowingData(
                lawn_id: $this->lawn->id,
                user_id: $this->user->id,
                height_mm: 45.5,
                collected: true
            ));
        });
    });

    describe('dependency injection', function (): void {
        test('implements CreateLawnCareActionContract', function (): void {
            expect($this->createLawnCareAction)
                ->toBeInstanceOf(CreateLawnCareActionContract::class);
        });
    });
});
