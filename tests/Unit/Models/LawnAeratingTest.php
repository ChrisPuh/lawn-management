<?php

declare(strict_types=1);

use App\Models\Lawn;
use App\Models\LawnAerating;
use App\Models\LawnImage;
use Illuminate\Support\Carbon;

describe('LawnAerating Model', function () {
    describe('attributes', function () {
        test('has correct fillable attributes', function () {
            $aerating = new LawnAerating;

            expect($aerating->getFillable())->toBe([
                'lawn_id',
                'aerated_on',
                'notes',
            ]);
        });

        test('converts to array with correct keys', function () {
            $aerating = LawnAerating::factory()->create();

            expect(array_keys($aerating->fresh()->toArray()))->toBe([
                'id',
                'lawn_id',
                'aerated_on',
                'notes',
                'created_at',
                'updated_at',
            ]);
        });

        test('uses correct table name', function () {
            $aerating = new LawnAerating;

            expect($aerating->getTable())->toBe('lawn_aeratings');
        });

        test('has correct attribute casts', function () {
            $aerating = new LawnAerating;
            $casts = $aerating->getCasts();

            // Sort both arrays to ensure consistent order
            ksort($casts);
            $expected = [
                'aerated_on' => 'date',
                'id' => 'int',
            ];
            ksort($expected);

            expect($casts)->toBe($expected);
        });

        test('casts aerated_on to Carbon instance', function () {
            $aerating = LawnAerating::factory()->create([
                'aerated_on' => '2024-01-15',
            ]);

            expect($aerating->aerated_on)->toBeInstanceOf(Carbon::class);
            expect($aerating->aerated_on->format('Y-m-d'))->toBe('2024-01-15');
        });
    });

    describe('relationships', function () {
        test('belongs to lawn', function () {
            $lawn = Lawn::factory()->create();
            $aerating = LawnAerating::factory()->create(['lawn_id' => $lawn->id]);

            expect($aerating->lawn)
                ->toBeInstanceOf(Lawn::class);
            expect($aerating->lawn->id)->toBe($lawn->id);
        });

        describe('images', function () {
            test('has morphMany relationship with LawnImage', function () {
                $aerating = LawnAerating::factory()->create();
                LawnImage::factory()->count(3)->create([
                    'imageable_id' => $aerating->id,
                    'imageable_type' => LawnAerating::class,
                ]);

                expect($aerating->images->count())->toBe(3);
                /** @var LawnImage $firstImage */
                $firstImage = $aerating->images->first();
                expect($firstImage)->toBeInstanceOf(LawnImage::class);
            });

            test('can access related images through relationship', function () {
                $aerating = LawnAerating::factory()->create();
                /** @var LawnImage $image */
                $image = LawnImage::factory()->create([
                    'imageable_id' => $aerating->id,
                    'imageable_type' => LawnAerating::class,
                ]);

                $aerating->refresh();

                /** @var LawnImage $firstImage */
                $firstImage = $aerating->images->first();
                expect($firstImage)->toBeInstanceOf(LawnImage::class);
                expect($firstImage->id)->toBe($image->id);
            });
        });
    });

    describe('factory', function () {
        test('can create aerating record using factory', function () {
            $aerating = LawnAerating::factory()->create();

            expect($aerating)->toBeInstanceOf(LawnAerating::class);
            expect($aerating->exists)->toBeTrue();
            expect($aerating->lawn)->toBeInstanceOf(Lawn::class);
        });

        test('can override attributes when creating', function () {
            $lawn = Lawn::factory()->create();
            $customDate = '2024-03-15';
            $customNotes = 'Test notes';

            $aerating = LawnAerating::factory()->create([
                'lawn_id' => $lawn->id,
                'aerated_on' => $customDate,
                'notes' => $customNotes,
            ]);

            expect($aerating->lawn_id)->toBe($lawn->id);
            expect($aerating->aerated_on->format('Y-m-d'))->toBe($customDate);
            expect($aerating->notes)->toBe($customNotes);
        });
    });

    describe('validation', function () {
        test('requires lawn_id to be present', function () {
            $aerating = LawnAerating::factory()->make(['lawn_id' => null]);

            expect(fn () => $aerating->save())
                ->toThrow(Illuminate\Database\QueryException::class);
        });

        test('requires aerated_on to be present', function () {
            $aerating = LawnAerating::factory()->make(['aerated_on' => null]);

            expect(fn () => $aerating->save())
                ->toThrow(Illuminate\Database\QueryException::class);
        });

        test('allows notes to be null', function () {
            $aerating = LawnAerating::factory()->create(['notes' => null]);

            expect($aerating->notes)->toBeNull();
        });
    });
});
