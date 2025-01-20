<?php

declare(strict_types=1);

use App\DataObjects\LawnCare\FertilizingData;
use App\Enums\LawnCare\WeatherCondition;

describe('FertilizingData', function () {
    $baseNutrients = [
        'nutrient_n' => 10.5,
        'nutrient_p' => 5.2,
        'nutrient_k' => 7.8,
    ];

    test('can be constructed with minimal required data', function () use ($baseNutrients) {
        $data = new FertilizingData(
            product_name: 'Test Fertilizer',
            amount_per_sqm: 0.5,
            nutrients: $baseNutrients
        );

        expect($data)
            ->product_name->toBe('Test Fertilizer')
            ->amount_per_sqm->toBe(0.5)
            ->nutrients->toBe($baseNutrients)
            ->watered->toBeFalse()
            ->temperature_celsius->toBeNull()
            ->weather_condition->toBeNull();
    });

    test('can be constructed with all optional data', function () use ($baseNutrients) {
        $data = new FertilizingData(
            product_name: 'Advanced Fertilizer',
            amount_per_sqm: 0.75,
            nutrients: $baseNutrients,
            watered: true,
            temperature_celsius: 22.5,
            weather_condition: WeatherCondition::SUNNY
        );

        expect($data)
            ->product_name->toBe('Advanced Fertilizer')
            ->amount_per_sqm->toBe(0.75)
            ->nutrients->toBe($baseNutrients)
            ->watered->toBeTrue()
            ->temperature_celsius->toBe(22.5)
            ->weather_condition->toBe(WeatherCondition::SUNNY);
    });

    describe('from method', function () use ($baseNutrients) {
        test('creates instance from array with minimal data', function () use ($baseNutrients) {
            $input = [
                'product_name' => 'Basic Fertilizer',
                'amount_per_sqm' => '0.5',
                'nutrients' => $baseNutrients,
            ];

            $data = FertilizingData::from($input);

            expect($data)
                ->product_name->toBe('Basic Fertilizer')
                ->amount_per_sqm->toBe(0.5)
                ->nutrients->toBe($baseNutrients)
                ->watered->toBeFalse()
                ->temperature_celsius->toBeNull()
                ->weather_condition->toBeNull();
        });

        test('creates instance from array with all data', function () use ($baseNutrients) {
            $input = [
                'product_name' => 'Complete Fertilizer',
                'amount_per_sqm' => '0.75',
                'nutrients' => $baseNutrients,
                'watered' => '1',
                'temperature_celsius' => '22.5',
                'weather_condition' => WeatherCondition::SUNNY->value,
            ];

            $data = FertilizingData::from($input);

            expect($data)
                ->product_name->toBe('Complete Fertilizer')
                ->amount_per_sqm->toBe(0.75)
                ->nutrients->toBe($baseNutrients)
                ->watered->toBeTrue()
                ->temperature_celsius->toBe(22.5)
                ->weather_condition->toBe(WeatherCondition::SUNNY);
        });

        test('handles optional fields with different input types', function () use ($baseNutrients) {
            $input = [
                'product_name' => 'Flexible Fertilizer',
                'amount_per_sqm' => 0.6,
                'nutrients' => $baseNutrients,
                'watered' => 1,
                'temperature_celsius' => '23',
                'weather_condition' => WeatherCondition::CLOUDY->value,
            ];

            $data = FertilizingData::from($input);

            expect($data)
                ->watered->toBeTrue()
                ->temperature_celsius->toBe(23.0)
                ->weather_condition->toBe(WeatherCondition::CLOUDY);
        });
    });

    describe('toArray method', function () use ($baseNutrients) {
        test('converts to array correctly', function () use ($baseNutrients) {
            $data = new FertilizingData(
                product_name: 'Export Fertilizer',
                amount_per_sqm: 0.5,
                nutrients: $baseNutrients,
                watered: true,
                temperature_celsius: 22.5,
                weather_condition: WeatherCondition::SUNNY
            );

            $array = $data->toArray();

            expect($array)->toBe([
                'product_name' => 'Export Fertilizer',
                'amount_per_sqm' => 0.5,
                'nutrients' => $baseNutrients,
                'watered' => true,
                'temperature_celsius' => 22.5,
                'weather_condition' => WeatherCondition::SUNNY->value,
            ]);
        });

        test('handles null weather condition in toArray', function () use ($baseNutrients) {
            $data = new FertilizingData(
                product_name: 'Null Weather Fertilizer',
                amount_per_sqm: 0.5,
                nutrients: $baseNutrients
            );

            $array = $data->toArray();

            expect($array)->toBe([
                'product_name' => 'Null Weather Fertilizer',
                'amount_per_sqm' => 0.5,
                'nutrients' => $baseNutrients,
                'watered' => false,
                'temperature_celsius' => null,
                'weather_condition' => null,
            ]);
        });
    });
});
