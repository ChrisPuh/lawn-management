<?php

declare(strict_types=1);

use App\DataObjects\LawnCare\MowingData;
use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\MowingPattern;

describe('MowingData', function (): void {
    test('can be constructed with minimal required data', function (): void {
        $data = new MowingData(
            height_mm: 45.5
        );

        expect($data)
            ->height_mm->toBe(45.5)
            ->pattern->toBeNull()
            ->collected->toBeTrue()
            ->blade_condition->toBeNull()
            ->duration_minutes->toBeNull();
    });

    test('can be constructed with all optional data', function (): void {
        $data = new MowingData(
            height_mm: 50.0,
            pattern: MowingPattern::DIAGONAL,
            collected: false,
            blade_condition: BladeCondition::DULL,
            duration_minutes: 30
        );

        expect($data)
            ->height_mm->toBe(50.0)
            ->pattern->toBe(MowingPattern::DIAGONAL)
            ->collected->toBeFalse()
            ->blade_condition->toBe(BladeCondition::DULL)
            ->duration_minutes->toBe(30);
    });

    test('throws exception for non-positive duration minutes', function (): void {
        expect(fn () => new MowingData(
            height_mm: 45.5,
            duration_minutes: 0
        ))->toThrow(InvalidArgumentException::class, 'Duration minutes must be positive')
            ->and(fn () => new MowingData(
                height_mm: 45.5,
                duration_minutes: -5
            ))->toThrow(InvalidArgumentException::class, 'Duration minutes must be positive');

    });

    describe('from method', function (): void {
        test('creates instance from array with minimal data', function (): void {
            $input = [
                'height_mm' => '45.5',
            ];

            $data = MowingData::from($input);

            expect($data)
                ->height_mm->toBe(45.5)
                ->pattern->toBeNull()
                ->collected->toBeTrue()
                ->blade_condition->toBeNull()
                ->duration_minutes->toBeNull();
        });

        test('creates instance from array with all data', function (): void {
            $input = [
                'height_mm' => '50.0',
                'pattern' => MowingPattern::DIAGONAL->value,
                'collected' => '0',
                'blade_condition' => BladeCondition::DULL->value,
                'duration_minutes' => '30',
            ];

            $data = MowingData::from($input);

            expect($data)
                ->height_mm->toBe(50.0)
                ->pattern->toBe(MowingPattern::DIAGONAL)
                ->collected->toBeFalse()
                ->blade_condition->toBe(BladeCondition::DULL)
                ->duration_minutes->toBe(30);
        });

        test('handles optional fields with different input types', function (): void {
            $input = [
                'height_mm' => 45.5,
                'collected' => 1,
                'duration_minutes' => '45',
            ];

            $data = MowingData::from($input);

            expect($data)
                ->height_mm->toBe(45.5)
                ->collected->toBeTrue()
                ->duration_minutes->toBe(45);
        });
    });

    describe('toArray method', function (): void {
        test('converts to array correctly with all fields', function (): void {
            $data = new MowingData(
                height_mm: 50.0,
                pattern: MowingPattern::DIAGONAL,
                collected: false,
                blade_condition: BladeCondition::DULL,
                duration_minutes: 30
            );

            $array = $data->toArray();

            expect($array)->toBe([
                'height_mm' => 50.0,
                'pattern' => MowingPattern::DIAGONAL->value,
                'collected' => false,
                'blade_condition' => BladeCondition::DULL->value,
                'duration_minutes' => 30,
            ]);
        });

        test('handles null enum values in toArray', function (): void {
            $data = new MowingData(
                height_mm: 45.5
            );

            $array = $data->toArray();

            expect($array)->toBe([
                'height_mm' => 45.5,
                'pattern' => null,
                'collected' => true,
                'blade_condition' => null,
                'duration_minutes' => null,
            ]);
        });
    });
});
