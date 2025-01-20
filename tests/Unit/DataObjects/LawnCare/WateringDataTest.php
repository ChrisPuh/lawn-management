<?php

declare(strict_types=1);

use App\DataObjects\LawnCare\WateringData;
use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;

describe('WateringData', function (): void {
    test('can be constructed with required data', function (): void {
        $data = new WateringData(
            amount_liters: 10.5,
            duration_minutes: 30,
            method: WateringMethod::MANUAL
        );

        expect($data)
            ->amount_liters->toBe(10.5)
            ->duration_minutes->toBe(30)
            ->method->toBe(WateringMethod::MANUAL)
            ->temperature_celsius->toBeNull()
            ->weather_condition->toBeNull()
            ->time_of_day->toBeNull();
    });

    test('can be constructed with all optional data', function (): void {
        $data = new WateringData(
            amount_liters: 15.0,
            duration_minutes: 45,
            method: WateringMethod::SPRINKLER,
            temperature_celsius: 22.5,
            weather_condition: WeatherCondition::SUNNY,
            time_of_day: TimeOfDay::MORNING
        );

        expect($data)
            ->amount_liters->toBe(15.0)
            ->duration_minutes->toBe(45)
            ->method->toBe(WateringMethod::SPRINKLER)
            ->temperature_celsius->toBe(22.5)
            ->weather_condition->toBe(WeatherCondition::SUNNY)
            ->time_of_day->toBe(TimeOfDay::MORNING);
    });

    test('throws exception for non-positive duration minutes', function (): void {
        expect(fn () => new WateringData(
            amount_liters: 10.5,
            duration_minutes: 0,
            method: WateringMethod::MANUAL
        ))->toThrow(InvalidArgumentException::class, 'Duration must be positive')
            ->and(fn () => new WateringData(
                amount_liters: 10.5,
                duration_minutes: -5,
                method: WateringMethod::MANUAL
            ))->toThrow(InvalidArgumentException::class, 'Duration must be positive');

    });

    describe('from method', function (): void {
        test('creates instance from array with minimal data', function (): void {
            $input = [
                'amount_liters' => '10.5',
                'duration_minutes' => '30',
                'method' => WateringMethod::MANUAL->value,
            ];

            $data = WateringData::from($input);

            expect($data)
                ->amount_liters->toBe(10.5)
                ->duration_minutes->toBe(30)
                ->method->toBe(WateringMethod::MANUAL)
                ->temperature_celsius->toBeNull()
                ->weather_condition->toBeNull()
                ->time_of_day->toBeNull();
        });

        test('creates instance from array with all data', function (): void {
            $input = [
                'amount_liters' => '15.0',
                'duration_minutes' => '45',
                'method' => WateringMethod::SPRINKLER->value,
                'temperature_celsius' => '22.5',
                'weather_condition' => WeatherCondition::SUNNY->value,
                'time_of_day' => TimeOfDay::MORNING->value,
            ];

            $data = WateringData::from($input);

            expect($data)
                ->amount_liters->toBe(15.0)
                ->duration_minutes->toBe(45)
                ->method->toBe(WateringMethod::SPRINKLER)
                ->temperature_celsius->toBe(22.5)
                ->weather_condition->toBe(WeatherCondition::SUNNY)
                ->time_of_day->toBe(TimeOfDay::MORNING);
        });

        test('handles optional fields with different input types', function (): void {
            $input = [
                'amount_liters' => 10.5,
                'duration_minutes' => '30',
                'method' => WateringMethod::MANUAL->value,
                'temperature_celsius' => 22,
                'weather_condition' => WeatherCondition::CLOUDY->value,
            ];

            $data = WateringData::from($input);

            expect($data)
                ->amount_liters->toBe(10.5)
                ->duration_minutes->toBe(30)
                ->method->toBe(WateringMethod::MANUAL)
                ->temperature_celsius->toBe(22.0)
                ->weather_condition->toBe(WeatherCondition::CLOUDY)
                ->time_of_day->toBeNull();
        });
    });

    describe('toArray method', function (): void {
        test('converts to array correctly with all fields', function (): void {
            $data = new WateringData(
                amount_liters: 15.0,
                duration_minutes: 45,
                method: WateringMethod::SPRINKLER,
                temperature_celsius: 22.5,
                weather_condition: WeatherCondition::SUNNY,
                time_of_day: TimeOfDay::MORNING
            );

            $array = $data->toArray();

            expect($array)->toBe([
                'amount_liters' => 15.0,
                'duration_minutes' => 45,
                'method' => WateringMethod::SPRINKLER->value,
                'temperature_celsius' => 22.5,
                'weather_condition' => WeatherCondition::SUNNY->value,
                'time_of_day' => TimeOfDay::MORNING->value,
            ]);
        });

        test('handles null enum values in toArray', function (): void {
            $data = new WateringData(
                amount_liters: 10.5,
                duration_minutes: 30,
                method: WateringMethod::MANUAL
            );

            $array = $data->toArray();

            expect($array)->toBe([
                'amount_liters' => 10.5,
                'duration_minutes' => 30,
                'method' => WateringMethod::MANUAL->value,
                'temperature_celsius' => null,
                'weather_condition' => null,
                'time_of_day' => null,
            ]);
        });
    });
});
