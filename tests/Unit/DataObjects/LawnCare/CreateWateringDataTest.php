<?php

declare(strict_types=1);

// tests/Unit/DataObjects/LawnCare/CreateWateringDataTest.php
use App\DataObjects\LawnCare\CreateWateringData;
use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;
use App\Http\Requests\BaseLawnCareRequest;
use App\Http\Requests\CreateWateringRequest;

describe('CreateWateringData', function () {
    it('can be instantiated with minimal data', function () {
        $data = new CreateWateringData(
            lawn_id: 1,
            user_id: 1,
            amount_liters: 10.5,
            duration_minutes: 30,
            method: WateringMethod::MANUAL,
        );

        expect($data)
            ->lawn_id->toBe(1)
            ->user_id->toBe(1)
            ->amount_liters->toBe(10.5)
            ->duration_minutes->toBe(30)
            ->method->toBe(WateringMethod::MANUAL)
            ->temperature_celsius->toBeNull()
            ->weather_condition->toBeNull()
            ->time_of_day->toBeNull()
            ->notes->toBeNull()
            ->performed_at->toBeNull()
            ->scheduled_for->toBeNull();
    });

    it('can be instantiated with all data', function () {
        $performedAt = new DateTime;
        $scheduledFor = new DateTime('+1 day');

        $data = new CreateWateringData(
            lawn_id: 1,
            user_id: 1,
            amount_liters: 10.5,
            duration_minutes: 30,
            method: WateringMethod::MANUAL,
            temperature_celsius: 22.5,
            weather_condition: WeatherCondition::SUNNY,
            time_of_day: TimeOfDay::MORNING,
            notes: 'Test notes',
            performed_at: $performedAt,
            scheduled_for: $scheduledFor,
        );

        expect($data)
            ->lawn_id->toBe(1)
            ->user_id->toBe(1)
            ->amount_liters->toBe(10.5)
            ->duration_minutes->toBe(30)
            ->method->toBe(WateringMethod::MANUAL)
            ->temperature_celsius->toBe(22.5)
            ->weather_condition->toBe(WeatherCondition::SUNNY)
            ->time_of_day->toBe(TimeOfDay::MORNING)
            ->notes->toBe('Test notes')
            ->performed_at->toBe($performedAt)
            ->scheduled_for->toBe($scheduledFor);
    });

    it('inherits base lawn care data properties', function () {
        $data = new CreateWateringData(
            lawn_id: 1,
            user_id: 1,
            amount_liters: 10.5,
            duration_minutes: 30,
            method: WateringMethod::MANUAL,
            notes: 'Test notes'
        );

        // Test inherited properties
        expect($data)
            ->lawn_id->toBe(1)
            ->user_id->toBe(1)
            ->notes->toBe('Test notes');
    });

    // Nur wenn wir Validierung im Constructor haben
    it('validates positive values', function () {
        expect(fn () => new CreateWateringData(
            lawn_id: 1,
            user_id: 1,
            amount_liters: -1, // negative amount
            duration_minutes: 30,
            method: WateringMethod::MANUAL,
        ))->toThrow(InvalidArgumentException::class)
            ->and(fn () => new CreateWateringData(
                lawn_id: 1,
                user_id: 1,
                amount_liters: 10.5,
                duration_minutes: -1, // negative duration
                method: WateringMethod::MANUAL,
            ))->toThrow(InvalidArgumentException::class);

    });

    // tests/Unit/DataObjects/LawnCare/CreateWateringDataTest.php

    it('creates from request', function () {

        $validatedData = [
            'lawn_id' => 1,
            'amount_liters' => '10.5',
            'duration_minutes' => '30',
            'method' => WateringMethod::MANUAL->value,
            'temperature_celsius' => null,
            'weather_condition' => null,
            'time_of_day' => null,
            'notes' => null,
            'performed_at' => '2024-01-19 10:00:00',
            'scheduled_for' => '2024-01-20 10:00:00',
        ];

        $data = CreateWateringData::fromArray($validatedData, 1);



        expect($data)
            ->toBeInstanceOf(CreateWateringData::class)
            ->lawn_id->toBe(1)
            ->user_id->toBe(1)
            ->amount_liters->toBe(10.5)
            ->duration_minutes->toBe(30)
            ->method->toBe(WateringMethod::MANUAL)
            ->temperature_celsius->toBeNull()
            ->weather_condition->toBeNull()
            ->time_of_day->toBeNull()
            ->notes->toBeNull()
            ->performed_at->toBeInstanceOf(DateTime::class)
            ->scheduled_for->toBeInstanceOf(DateTime::class);
    });

});
