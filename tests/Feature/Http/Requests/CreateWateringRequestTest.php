<?php

// tests/Unit/Http/Requests/CreateWateringRequestTest.php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests;

use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;
use App\Http\Requests\CreateWateringRequest;
use App\Models\Lawn;
use Illuminate\Support\Facades\Validator;

describe('CreateWateringRequest Validation', function (): void {
    it('fails with empty data', function (): void {
        $request = new CreateWateringRequest;
        $validator = Validator::make([], $request->rules());

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->keys())->toContain(
                'lawn_id',
                'amount_liters',
                'duration_minutes',
                'method'
            );
    });

    it('fails with invalid types', function (): void {
        $request = new CreateWateringRequest;
        $validator = Validator::make([
            'lawn_id' => 'not-a-number',
            'amount_liters' => 'not-a-number',
            'duration_minutes' => 'not-a-number',
            'method' => 'invalid-method',
            'temperature_celsius' => 'not-a-number',
        ], $request->rules());

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->keys())->toContain(
                'lawn_id',
                'amount_liters',
                'duration_minutes',
                'method',
                'temperature_celsius'
            );
    });

    it('validates with minimal data', function (): void {
        $request = new CreateWateringRequest;
        $validator = Validator::make([
            'lawn_id' => Lawn::factory()->create()->id,
            'amount_liters' => 10.5,
            'duration_minutes' => 30,
            'method' => WateringMethod::MANUAL->value,
        ], $request->rules());

        expect($validator->fails())->toBeFalse();
    });

    it('validates with complete data', function (): void {
        $request = new CreateWateringRequest;
        $validator = Validator::make([
            'lawn_id' => Lawn::factory()->create()->id,
            'amount_liters' => 10.5,
            'duration_minutes' => 30,
            'method' => WateringMethod::MANUAL->value,
            'temperature_celsius' => 22.5,
            'weather_condition' => WeatherCondition::SUNNY->value,
            'time_of_day' => TimeOfDay::MORNING->value,
            'notes' => 'Test notes',
            'performed_at' => now()->toDateTimeString(),
            'scheduled_for' => now()->addDay()->toDateTimeString(),
        ], $request->rules());

        expect($validator->fails())->toBeFalse();
    });

    it('prevents negative values', function (): void {
        $request = new CreateWateringRequest;
        $validator = Validator::make([
            'lawn_id' => Lawn::factory()->create()->id,
            'amount_liters' => -1,
            'duration_minutes' => -1,
            'method' => WateringMethod::MANUAL->value,
        ], $request->rules());

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->keys())->toContain(
                'amount_liters',
                'duration_minutes'
            );
    });

    it('validates date formats', function (): void {
        $request = new CreateWateringRequest;
        $validator = Validator::make([
            'lawn_id' => Lawn::factory()->create()->id,
            'amount_liters' => 10.5,
            'duration_minutes' => 30,
            'method' => WateringMethod::MANUAL->value,
            'performed_at' => 'invalid-date',
            'scheduled_for' => 'invalid-date',
        ], $request->rules());

        expect($validator->fails())->toBeTrue()
            ->and($validator->errors()->keys())->toContain(
                'performed_at',
                'scheduled_for'
            );
    });
});
