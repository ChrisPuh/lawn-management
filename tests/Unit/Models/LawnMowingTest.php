<?php

declare(strict_types=1);

use App\Models\Lawn;
use App\Models\LawnMowing;
use Illuminate\Support\Carbon;

describe('LawnMowing Model', function () {
    describe('attributes', function () {
        test('has correct fillable attributes', function () {
            $mowing = new LawnMowing;

            expect($mowing->getFillable())->toBe([
                'lawn_id',
                'mowed_on',
                'cutting_height',
                'notes',
            ]);
        });

        test('converts to array with correct keys', function () {
            $mowing = LawnMowing::factory()->create();

            expect(array_keys($mowing->fresh()->toArray()))->toBe([
                'id',
                'lawn_id',
                'mowed_on',
                'cutting_height',
                'notes',
                'created_at',
                'updated_at',
            ]);
        });

        test('uses correct table name', function () {
            $mowing = new LawnMowing;

            expect($mowing->getTable())->toBe('lawn_mowings');
        });

        test('has correct attribute casts', function () {
            $mowing = new LawnMowing;
            $casts = $mowing->getCasts();

            // Sort both arrays to ensure consistent order
            ksort($casts);
            $expected = [
                'cutting_height' => 'string',
                'mowed_on' => 'date',
                'id' => 'int',
            ];
            ksort($expected);

            expect($casts)->toBe($expected);
        });

        describe('casting', function () {
            test('casts mowed_on to Carbon instance', function () {
                $mowing = LawnMowing::factory()->create([
                    'mowed_on' => '2024-01-15',
                ]);

                expect($mowing->mowed_on)->toBeInstanceOf(Carbon::class);
                expect($mowing->mowed_on->format('Y-m-d'))->toBe('2024-01-15');
            });

            test('casts cutting_height to string', function () {
                $mowing = LawnMowing::factory()->create([
                    'cutting_height' => '45mm',
                ]);

                expect($mowing->cutting_height)
                    ->toBeString()
                    ->toBe('45mm');
            });

            test('allows cutting_height to be null', function () {
                $mowing = LawnMowing::factory()->create([
                    'cutting_height' => null,
                ]);

                expect($mowing->cutting_height)->toBeNull();
            });
        });
    });

    describe('relationships', function () {
        test('belongs to lawn', function () {
            $lawn = Lawn::factory()->create();
            $mowing = LawnMowing::factory()->create(['lawn_id' => $lawn->getKey()]);

            expect($mowing->lawn)->toBeInstanceOf(Lawn::class);
            expect($mowing->lawn->getKey())->toBe($lawn->getKey());
        });
    });

    describe('factory', function () {
        test('can create mowing record using factory', function () {
            $mowing = LawnMowing::factory()->create();

            expect($mowing)->toBeInstanceOf(LawnMowing::class);
            expect($mowing->exists)->toBeTrue();
            expect($mowing->lawn)->toBeInstanceOf(Lawn::class);
        });

        test('can override attributes when creating', function () {
            $lawn = Lawn::factory()->create();
            $customDate = '2024-03-15';
            $customHeight = '35mm';
            $customNotes = 'Test notes';

            $mowing = LawnMowing::factory()->create([
                'lawn_id' => $lawn->getKey(),
                'mowed_on' => $customDate,
                'cutting_height' => $customHeight,
                'notes' => $customNotes,
            ]);

            expect($mowing->lawn_id)->toBe($lawn->getKey());
            expect($mowing->mowed_on->format('Y-m-d'))->toBe($customDate);
            expect($mowing->cutting_height)->toBe($customHeight);
            expect($mowing->notes)->toBe($customNotes);
        });
    });

    describe('validation', function () {
        test('requires lawn_id to be present', function () {
            $mowing = LawnMowing::factory()->make(['lawn_id' => null]);

            expect(fn () => $mowing->save())
                ->toThrow(Illuminate\Database\QueryException::class);
        });

        test('requires mowed_on to be present', function () {
            $mowing = LawnMowing::factory()->make(['mowed_on' => null]);

            expect(fn () => $mowing->save())
                ->toThrow(Illuminate\Database\QueryException::class);
        });

        test('allows cutting_height to be null', function () {
            $mowing = LawnMowing::factory()->create(['cutting_height' => null]);

            expect($mowing->cutting_height)->toBeNull();
        });

        test('allows notes to be null', function () {
            $mowing = LawnMowing::factory()->create(['notes' => null]);

            expect($mowing->notes)->toBeNull();
        });
    });

    describe('edge cases', function () {
        test('accepts various cutting height formats', function () {
            $formats = ['35mm', '3.5cm', '1.5"'];

            foreach ($formats as $format) {
                $mowing = LawnMowing::factory()->create(['cutting_height' => $format]);

                expect($mowing->cutting_height)->toBe($format);
            }
        });
    });
});
