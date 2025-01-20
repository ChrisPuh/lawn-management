<?php
// tests/Unit/DataObjects/LawnCare/CreateFertilizingDataTest.php

declare(strict_types=1);

use App\DataObjects\LawnCare\CreateFertilizingData;
use App\Enums\LawnCare\WeatherCondition;

describe('CreateFertilizingData', function () {
    it('can be instantiated with minimal data', function () {
        $data = new CreateFertilizingData(
            lawn_id: 1,
            user_id: 1,
            product_name: 'Green Boost Fertilizer',
            amount_per_sqm: 0.5,
            nutrients: ['N', 'P', 'K'],
            watered: false
        );

        expect($data)
            ->lawn_id->toBe(1)
            ->user_id->toBe(1)
            ->product_name->toBe('Green Boost Fertilizer')
            ->amount_per_sqm->toBe(0.5)
            ->nutrients->toBe(['N', 'P', 'K'])
            ->watered->toBeFalse()
            ->temperature_celsius->toBeNull()
            ->weather_condition->toBeNull()
            ->notes->toBeNull()
            ->performed_at->toBeNull()
            ->scheduled_for->toBeNull();
    });

    it('can be instantiated with all data', function () {
        $performedAt = new DateTime();
        $scheduledFor = new DateTime('+1 day');

        $data = new CreateFertilizingData(
            lawn_id: 1,
            user_id: 1,
            product_name: 'Advanced Lawn Nutrition',
            amount_per_sqm: 0.75,
            nutrients: ['N', 'P', 'K', 'Fe'],
            watered: true,
            temperature_celsius: 22.5,
            weather_condition: WeatherCondition::SUNNY,
            notes: 'Spring fertilization',
            performed_at: $performedAt,
            scheduled_for: $scheduledFor
        );

        expect($data)
            ->lawn_id->toBe(1)
            ->user_id->toBe(1)
            ->product_name->toBe('Advanced Lawn Nutrition')
            ->amount_per_sqm->toBe(0.75)
            ->nutrients->toBe(['N', 'P', 'K', 'Fe'])
            ->watered->toBeTrue()
            ->temperature_celsius->toBe(22.5)
            ->weather_condition->toBe(WeatherCondition::SUNNY)
            ->notes->toBe('Spring fertilization')
            ->performed_at->toBe($performedAt)
            ->scheduled_for->toBe($scheduledFor);
    });

    it('creates from array with all fields', function () {
        $validatedData = [
            'lawn_id' => 1,
            'product_name' => 'Complete Lawn Fertilizer',
            'amount_per_sqm' => '0.6',
            'nutrients' => ['N', 'P', 'K', 'Ca'],
            'watered' => true,
            'temperature_celsius' => '25.3',
            'weather_condition' => WeatherCondition::SUNNY->value,
            'notes' => 'Summer fertilization',
            'performed_at' => '2024-01-19 14:30:00',
            'scheduled_for' => '2024-01-20 15:00:00',
        ];

        $data = CreateFertilizingData::fromArray($validatedData, 1);

        expect($data)
            ->toBeInstanceOf(CreateFertilizingData::class)
            ->lawn_id->toBe(1)
            ->user_id->toBe(1)
            ->product_name->toBe('Complete Lawn Fertilizer')
            ->amount_per_sqm->toBe(0.6)
            ->nutrients->toBe(['N', 'P', 'K', 'Ca'])
            ->watered->toBeTrue()
            ->temperature_celsius->toBe(25.3)
            ->weather_condition->toBe(WeatherCondition::SUNNY)
            ->notes->toBe('Summer fertilization')
            ->performed_at->toBeInstanceOf(DateTime::class)
            ->scheduled_for->toBeInstanceOf(DateTime::class);
    });

    it('handles optional fields from array', function () {
        $validatedData = [
            'lawn_id' => 1,
            'product_name' => 'Basic Lawn Food',
            'amount_per_sqm' => '0.5',
            'nutrients' => ['N', 'P'],
            'watered' => false,
        ];

        $data = CreateFertilizingData::fromArray($validatedData, 1);

        expect($data)
            ->product_name->toBe('Basic Lawn Food')
            ->amount_per_sqm->toBe(0.5)
            ->nutrients->toBe(['N', 'P'])
            ->watered->toBeFalse()
            ->temperature_celsius->toBeNull()
            ->weather_condition->toBeNull()
            ->notes->toBeNull()
            ->performed_at->toBeNull()
            ->scheduled_for->toBeNull();
    });
});
