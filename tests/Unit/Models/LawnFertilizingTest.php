<?php

declare(strict_types=1);

use App\Models\Lawn;
use App\Models\LawnFertilizing;
use App\Models\LawnImage;
use Illuminate\Support\Carbon;

describe('LawnFertilizing Model', function () {
    describe('attributes', function () {
        test('has correct fillable attributes', function () {
            $fertilizing = new LawnFertilizing;

            expect($fertilizing->getFillable())->toBe([
                'lawn_id',
                'fertilized_on',
                'fertilizer_name',
                'quantity',
                'quantity_unit',
                'notes',
            ]);
        });

        test('converts to array with correct keys', function () {
            $fertilizing = LawnFertilizing::factory()->create();

            expect(array_keys($fertilizing->fresh()->toArray()))->toBe([
                'id',
                'lawn_id',
                'fertilized_on',
                'fertilizer_name',
                'quantity',
                'quantity_unit',
                'notes',
                'created_at',
                'updated_at',
            ]);
        });

        test('uses correct table name', function () {
            $fertilizing = new LawnFertilizing;

            expect($fertilizing->getTable())->toBe('lawn_fertilizings');
        });

        test('has correct attribute casts', function () {
            $fertilizing = new LawnFertilizing;
            $casts = $fertilizing->getCasts();

            // Sort both arrays to ensure consistent order
            ksort($casts);
            $expected = [
                'fertilized_on' => 'date',
                'quantity' => 'decimal:2',
                'id' => 'int',
            ];
            ksort($expected);

            expect($casts)->toBe($expected);
        });

        test('casts fertilized_on to Carbon instance', function () {
            $fertilizing = LawnFertilizing::factory()->create([
                'fertilized_on' => '2024-01-15',
            ]);

            expect($fertilizing->fertilized_on)->toBeInstanceOf(Carbon::class);
            expect($fertilizing->fertilized_on->format('Y-m-d'))->toBe('2024-01-15');
        });

        test('casts quantity to decimal with 2 decimal places', function () {
            $fertilizing = LawnFertilizing::factory()->create([
                'quantity' => 2.5,
            ]);

            expect($fertilizing->quantity)->toBe('2.50');
            expect(is_string($fertilizing->quantity))->toBeTrue();
        });

        test('stores quantity_unit as string', function () {
            $fertilizing = LawnFertilizing::factory()->create([
                'quantity_unit' => 'kg',
            ]);

            expect($fertilizing->quantity_unit)
                ->toBe('kg')
                ->toBeString();
        });
    });

    describe('relationships', function () {
        test('belongs to lawn', function () {
            $lawn = Lawn::factory()->create();
            $fertilizing = LawnFertilizing::factory()->create(['lawn_id' => $lawn->id]);

            expect($fertilizing->lawn)->toBeInstanceOf(Lawn::class);
            expect($fertilizing->lawn->id)->toBe($lawn->id);
        });

        describe('images', function () {
            test('has morphMany relationship with LawnImage', function () {
                $fertilizing = LawnFertilizing::factory()->create();
                LawnImage::factory()->count(3)->create([
                    'imageable_id' => $fertilizing->id,
                    'imageable_type' => LawnFertilizing::class,
                ]);

                expect($fertilizing->images->count())->toBe(3);
                /** @var LawnImage $firstImage */
                $firstImage = $fertilizing->images->first();
                expect($firstImage)->toBeInstanceOf(LawnImage::class);
            });

            test('can access related images through relationship', function () {
                $fertilizing = LawnFertilizing::factory()->create();
                /** @var LawnImage $image */
                $image = LawnImage::factory()->create([
                    'imageable_id' => $fertilizing->id,
                    'imageable_type' => LawnFertilizing::class,
                ]);

                $fertilizing->refresh();

                /** @var LawnImage $firstImage */
                $firstImage = $fertilizing->images->first();
                expect($firstImage)->toBeInstanceOf(LawnImage::class);
                expect($firstImage->id)->toBe($image->id);
            });
        });
    });

    describe('factory', function () {
        test('can create fertilizing record using factory', function () {
            $fertilizing = LawnFertilizing::factory()->create();

            expect($fertilizing)->toBeInstanceOf(LawnFertilizing::class);
            expect($fertilizing->exists)->toBeTrue();
            expect($fertilizing->lawn)->toBeInstanceOf(Lawn::class);
        });

        test('can override attributes when creating', function () {
            $lawn = Lawn::factory()->create();
            $customDate = '2024-03-15';
            $customNotes = 'Test notes';
            $customQuantity = 3.75;
            $customUnit = 'kg';
            $customFertilizer = 'Test Fertilizer';

            $fertilizing = LawnFertilizing::factory()->create([
                'lawn_id' => $lawn->id,
                'fertilized_on' => $customDate,
                'notes' => $customNotes,
                'quantity' => $customQuantity,
                'quantity_unit' => $customUnit,
                'fertilizer_name' => $customFertilizer,
            ]);

            expect($fertilizing->lawn_id)->toBe($lawn->id);
            expect($fertilizing->fertilized_on->format('Y-m-d'))->toBe($customDate);
            expect($fertilizing->notes)->toBe($customNotes);
            expect($fertilizing->quantity)->toBe('3.75');
            expect($fertilizing->quantity_unit)->toBe($customUnit);
            expect($fertilizing->fertilizer_name)->toBe($customFertilizer);
        });
    });

    describe('validation', function () {
        test('requires lawn_id to be present', function () {
            $fertilizing = LawnFertilizing::factory()->make(['lawn_id' => null]);

            expect(fn () => $fertilizing->save())
                ->toThrow(Illuminate\Database\QueryException::class);
        });

        test('allows fertilized_on to be null', function () {
            $fertilizing = LawnFertilizing::factory()->make(['fertilized_on' => null]);

            expect($fertilizing->fertilized_on)->toBeNull();
        });

        test('allows fertilizer_name to be null', function () {
            $fertilizing = LawnFertilizing::factory()->make(['fertilizer_name' => null]);

            expect($fertilizing->fertilizer_name)->toBeNull();
        });

        test('allows quantity to be null', function () {
            $fertilizing = LawnFertilizing::factory()->make(['quantity' => null]);

            expect($fertilizing->quantity)->toBeNull();
        });

        test('allows notes to be null', function () {
            $fertilizing = LawnFertilizing::factory()->create(['notes' => null]);

            expect($fertilizing->notes)->toBeNull();
        });
    });
});
