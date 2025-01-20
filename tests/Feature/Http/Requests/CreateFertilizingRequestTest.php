<?php

// tests/Unit/Http/Requests/CreateFertilizingRequestTest.php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\CreateFertilizingRequest;
use App\Models\Lawn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

describe('CreateFertilizingRequest', function () {
    describe('validation rules', function () {
        beforeEach(function () {
            $this->lawn = Lawn::factory()->create();
        });

        test('accepts valid data', function () {
            $validator = Validator::make([
                'lawn_id' => $this->lawn->id,
                'height_mm' => 45,
                'product_name' => 'Test Fertilizer',
                'amount_per_sqm' => 25.5,
                'nutrients' => 'NPK 12-8-16',
                'watered' => true,
                'temperature_celsius' => 22.5,
                'weather_condition' => 'sunny',
                'notes' => 'Test note',
                'performed_at' => now()->toDateTimeString(),
                'scheduled_for' => now()->addDay()->toDateTimeString(),
            ], (new CreateFertilizingRequest)->rules());

            expect($validator->fails())->toBeFalse();
        });

        test('accepts minimal data', function () {
            $validator = Validator::make([
                'lawn_id' => $this->lawn->id,
                'height_mm' => 45,
                'product_name' => 'Test Fertilizer',
                'amount_per_sqm' => 25.5,
                'nutrients' => 'NPK 12-8-16',
            ], (new CreateFertilizingRequest)->rules());

            expect($validator->fails())->toBeFalse();
        });

        test('validates required fields', function () {
            $validator = Validator::make([], (new CreateFertilizingRequest)->rules());

            expect($validator->fails())->toBeTrue()
                ->and($validator->errors()->keys())->toContain(
                    'lawn_id',
                    'height_mm',
                    'product_name',
                    'amount_per_sqm',
                    'nutrients'
                );
        });

        describe('height_mm', function () {
            test('must be at least 20mm', function () {
                $validator = Validator::make([
                    'height_mm' => 19,
                ], (new CreateFertilizingRequest)->rules());

                expect($validator->errors()->has('height_mm'))->toBeTrue();
            });

            test('cannot exceed 100mm', function () {
                $validator = Validator::make([
                    'height_mm' => 101,
                ], (new CreateFertilizingRequest)->rules());

                expect($validator->errors()->has('height_mm'))->toBeTrue();
            });
        });

        describe('product_name', function () {
            test('must not exceed 255 characters', function () {
                $validator = Validator::make([
                    'product_name' => str_repeat('a', 256),
                ], (new CreateFertilizingRequest)->rules());

                expect($validator->errors()->has('product_name'))->toBeTrue();
            });
        });

        describe('amount_per_sqm', function () {
            test('must be positive', function () {
                $validator = Validator::make([
                    'amount_per_sqm' => 0,
                ], (new CreateFertilizingRequest)->rules());

                expect($validator->errors()->has('amount_per_sqm'))->toBeTrue();
            });

            test('cannot exceed 1000', function () {
                $validator = Validator::make([
                    'amount_per_sqm' => 1001,
                ], (new CreateFertilizingRequest)->rules());

                expect($validator->errors()->has('amount_per_sqm'))->toBeTrue();
            });
        });

        describe('nutrients', function () {
            test('must not exceed 255 characters', function () {
                $validator = Validator::make([
                    'nutrients' => str_repeat('a', 256),
                ], (new CreateFertilizingRequest)->rules());

                expect($validator->errors()->has('nutrients'))->toBeTrue();
            });
        });

        describe('watered', function () {
            test('must be boolean', function () {
                $validator = Validator::make([
                    'watered' => 'not-a-boolean',
                ], (new CreateFertilizingRequest)->rules());

                expect($validator->errors()->has('watered'))->toBeTrue();

                $validator = Validator::make([
                    'watered' => true,
                ], (new CreateFertilizingRequest)->rules());

                expect($validator->errors()->has('watered'))->toBeFalse();
            });
        });

        describe('temperature_celsius', function () {
            test('validates range', function () {
                $validator = Validator::make([
                    'temperature_celsius' => -21,
                ], (new CreateFertilizingRequest)->rules());

                expect($validator->errors()->has('temperature_celsius'))->toBeTrue();

                $validator = Validator::make([
                    'temperature_celsius' => 51,
                ], (new CreateFertilizingRequest)->rules());

                expect($validator->errors()->has('temperature_celsius'))->toBeTrue();
            });

            test('is optional', function () {
                $validator = Validator::make([
                    'lawn_id' => $this->lawn->id,
                    'height_mm' => 45,
                    'product_name' => 'Test Fertilizer',
                    'amount_per_sqm' => 25.5,
                    'nutrients' => 'NPK 12-8-16',
                ], (new CreateFertilizingRequest)->rules());

                expect($validator->errors()->has('temperature_celsius'))->toBeFalse();
            });
        });

        describe('weather_condition', function () {
            test('must not exceed 255 characters', function () {
                $validator = Validator::make([
                    'weather_condition' => str_repeat('a', 256),
                ], (new CreateFertilizingRequest)->rules());

                expect($validator->errors()->has('weather_condition'))->toBeTrue();
            });

            test('is optional', function () {
                $validator = Validator::make([
                    'lawn_id' => $this->lawn->id,
                    'height_mm' => 45,
                    'product_name' => 'Test Fertilizer',
                    'amount_per_sqm' => 25.5,
                    'nutrients' => 'NPK 12-8-16',
                ], (new CreateFertilizingRequest)->rules());

                expect($validator->errors()->has('weather_condition'))->toBeFalse();
            });
        });
    });
});
