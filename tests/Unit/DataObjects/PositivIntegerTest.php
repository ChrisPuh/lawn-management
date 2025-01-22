<?php

declare(strict_types=1);

use App\DataObjects\PositiveInteger;

describe('PositiveInteger', function (): void {
    describe('from method', function (): void {
        test('creates instance with positive integer', function (): void {
            $positiveInt = PositiveInteger::from(5);
            expect($positiveInt->value())->toBe(5);

            $positiveInt = PositiveInteger::from('10');
            expect($positiveInt->value())->toBe(10);
        });

        test('throws exception for zero', function (): void {
            expect(fn () => PositiveInteger::from(0))
                ->toThrow(InvalidArgumentException::class, 'Value must be positive');
        });

        test('throws exception for negative number', function (): void {
            expect(fn () => PositiveInteger::from(-5))
                ->toThrow(InvalidArgumentException::class, 'Value must be positive')
                ->and(fn () => PositiveInteger::from('-10'))
                ->toThrow(InvalidArgumentException::class, 'Value must be positive');

        });
    });

    describe('tryFrom method', function (): void {
        test('returns PositiveInteger for valid positive integer', function (): void {
            $positiveInt = PositiveInteger::tryFrom(5);
            expect($positiveInt)->toBeInstanceOf(PositiveInteger::class)
                ->and($positiveInt?->value())->toBe(5);

            $positiveInt = PositiveInteger::tryFrom('10');
            expect($positiveInt)->toBeInstanceOf(PositiveInteger::class)
                ->and($positiveInt?->value())->toBe(10);
        });

        test('returns null for zero', function (): void {
            $result = PositiveInteger::tryFrom(0);
            expect($result)->toBeNull();
        });

        test('returns null for negative number', function (): void {
            $result = PositiveInteger::tryFrom(-5);
            expect($result)->toBeNull();

            $result = PositiveInteger::tryFrom('-10');
            expect($result)->toBeNull();
        });

        test('returns null for null input', function (): void {
            $result = PositiveInteger::tryFrom(null);
            expect($result)->toBeNull();
        });
    });

    describe('value method', function (): void {
        test('returns the correct integer value', function (): void {
            $positiveInt = PositiveInteger::from(42);
            expect($positiveInt->value())->toBe(42);

            $positiveInt = PositiveInteger::from('7');
            expect($positiveInt->value())->toBe(7);
        });
    });
});
