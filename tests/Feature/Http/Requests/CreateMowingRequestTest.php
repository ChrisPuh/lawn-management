<?php

// tests/Unit/Http/Requests/CreateMowingRequestTest.php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests;

use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\MowingPattern;
use App\Http\Requests\CreateMowingRequest;
use App\Models\Lawn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

describe('CreateMowingRequest', function () {
    describe('validation rules', function () {
        beforeEach(function () {
            $this->lawn = Lawn::factory()->create();
        });

        test('accepts valid data', function () {
            $validator = Validator::make([
                'lawn_id' => $this->lawn->id,  // muss existieren
                'height_mm' => 45,
                'pattern' => MowingPattern::DIAGONAL->value,
                'collected' => true,
                'blade_condition' => BladeCondition::SHARP->value,
                'duration_minutes' => 30,
                'notes' => 'Test note',
                'performed_at' => now()->toDateTimeString(),
                'scheduled_for' => now()->addDay()->toDateTimeString(),
            ], (new CreateMowingRequest)->rules());

            expect($validator->fails())->toBeFalse();
        });

        test('accepts minimal data', function () {
            $validator = Validator::make([
                'lawn_id' => $this->lawn->id,  // muss existieren
                'height_mm' => 45,
                'collected' => true,
            ], (new CreateMowingRequest)->rules());

            expect($validator->fails())->toBeFalse();
        });

        test('validates required fields', function () {
            $validator = Validator::make([], (new CreateMowingRequest)->rules());

            expect($validator->fails())->toBeTrue()
                ->and($validator->errors()->keys())->toContain(
                    'lawn_id',
                    'height_mm'
                );
        });

        describe('height_mm', function () {
            test('must be at least 20mm', function () {
                $validator = Validator::make([
                    'height_mm' => 19,
                ], (new CreateMowingRequest)->rules());

                expect($validator->errors()->has('height_mm'))->toBeTrue();
            });

            test('cannot exceed 100mm', function () {
                $validator = Validator::make([
                    'height_mm' => 101,
                ], (new CreateMowingRequest)->rules());

                expect($validator->errors()->has('height_mm'))->toBeTrue();
            });
        });

        describe('duration_minutes', function () {
            test('must be positive when provided', function () {
                $validator = Validator::make([
                    'duration_minutes' => 0,
                ], (new CreateMowingRequest)->rules());

                expect($validator->errors()->has('duration_minutes'))->toBeTrue();
            });

            test('must be integer', function () {
                $validator = Validator::make([
                    'duration_minutes' => 1.5,
                ], (new CreateMowingRequest)->rules());

                expect($validator->errors()->has('duration_minutes'))->toBeTrue();
            });
        });

        describe('collected', function () {
            test('must be boolean', function () {
                $validator = Validator::make([
                    'collected' => 'not-a-boolean',
                ], (new CreateMowingRequest)->rules());

                expect($validator->errors()->has('collected'))->toBeTrue();

                $validator = Validator::make([
                    'collected' => true,
                ], (new CreateMowingRequest)->rules());

                expect($validator->errors()->has('collected'))->toBeFalse();
            });
        });

        describe('enums', function () {
            test('validates pattern enum', function () {
                $validator = Validator::make([
                    'pattern' => 'invalid-pattern',
                ], (new CreateMowingRequest)->rules());

                expect($validator->errors()->has('pattern'))->toBeTrue();

                $validator = Validator::make([
                    'pattern' => MowingPattern::DIAGONAL->value,
                ], (new CreateMowingRequest)->rules());

                expect($validator->errors()->has('blade_condition'))->toBeFalse();
            });
        });
    });
});
