<?php

declare(strict_types=1);

use App\Models\Lawn;
use App\Models\LawnImage;
use App\Models\LawnScarifying;
use Illuminate\Support\Carbon;

describe('LawnScarifying Model', function (): void {
    describe('attributes', function (): void {
        test('has correct fillable attributes', function (): void {
            $scarifying = new LawnScarifying;

            expect($scarifying->getFillable())->toBe([
                'lawn_id',
                'scarified_on',
                'notes',
            ]);
        });

        test('converts to array with correct keys', function (): void {
            $scarifying = LawnScarifying::factory()->create();

            expect(array_keys($scarifying->fresh()->toArray()))->toBe([
                'id',
                'lawn_id',
                'scarified_on',
                'notes',
                'created_at',
                'updated_at',
            ]);
        });

        test('uses correct table name', function (): void {
            $scarifying = new LawnScarifying;

            expect($scarifying->getTable())->toBe('lawn_scarifyings');
        });

        test('has correct attribute casts', function (): void {
            $scarifying = new LawnScarifying;
            $casts = $scarifying->getCasts();

            // Sort both arrays to ensure consistent order
            ksort($casts);
            $expected = [
                'scarified_on' => 'date',
                'id' => 'int',
            ];
            ksort($expected);

            expect($casts)->toBe($expected);
        });

        test('casts scarified_on to Carbon instance', function (): void {
            $scarifying = LawnScarifying::factory()->create([
                'scarified_on' => '2024-01-15',
            ]);

            expect($scarifying->scarified_on)->toBeInstanceOf(Carbon::class);
            expect($scarifying->scarified_on->format('Y-m-d'))->toBe('2024-01-15');
        });
    });

    describe('relationships', function (): void {
        test('belongs to lawn', function (): void {
            $lawn = Lawn::factory()->create();
            $scarifying = LawnScarifying::factory()->create(['lawn_id' => $lawn->getKey()]);

            expect($scarifying->lawn)->toBeInstanceOf(Lawn::class);
            expect($scarifying->lawn->getKey())->toBe($lawn->getKey());
        });

        describe('images', function (): void {
            test('has morphMany relationship with LawnImage', function (): void {
                $scarifying = LawnScarifying::factory()->create();
                LawnImage::factory()->count(3)->create([
                    'imageable_id' => $scarifying->getKey(),
                    'imageable_type' => LawnScarifying::class,
                ]);

                expect($scarifying->images->count())->toBe(3);
                expect($scarifying->images->first())->toBeInstanceOf(LawnImage::class);
            });

            test('can access related images through relationship', function (): void {
                $scarifying = LawnScarifying::factory()->create();
                $image = LawnImage::factory()->create([
                    'imageable_id' => $scarifying->getKey(),
                    'imageable_type' => LawnScarifying::class,
                ]);

                $scarifying->refresh();

                expect($scarifying->images->first()->getKey())->toBe($image->getKey());
            });
        });
    });

    describe('factory', function (): void {
        test('can create scarifying record using factory', function (): void {
            $scarifying = LawnScarifying::factory()->create();

            expect($scarifying)->toBeInstanceOf(LawnScarifying::class);
            expect($scarifying->exists)->toBeTrue();
            expect($scarifying->lawn)->toBeInstanceOf(Lawn::class);
        });

        test('can override attributes when creating', function (): void {
            $lawn = Lawn::factory()->create();
            $customDate = '2024-03-15';
            $customNotes = 'Test notes';

            $scarifying = LawnScarifying::factory()->create([
                'lawn_id' => $lawn->getKey(),
                'scarified_on' => $customDate,
                'notes' => $customNotes,
            ]);

            expect($scarifying->lawn_id)->toBe($lawn->getKey());
            expect($scarifying->scarified_on->format('Y-m-d'))->toBe($customDate);
            expect($scarifying->notes)->toBe($customNotes);
        });
    });

    describe('validation', function (): void {
        test('requires lawn_id to be present', function (): void {
            $scarifying = LawnScarifying::factory()->make(['lawn_id' => null]);

            expect(fn () => $scarifying->save())
                ->toThrow(Illuminate\Database\QueryException::class);
        });

        test('requires scarified_on to be present', function (): void {
            $scarifying = LawnScarifying::factory()->make(['scarified_on' => null]);

            expect(fn () => $scarifying->save())
                ->toThrow(Illuminate\Database\QueryException::class);
        });

        test('allows notes to be null', function (): void {
            $scarifying = LawnScarifying::factory()->create(['notes' => null]);

            expect($scarifying->notes)->toBeNull();
        });
    });
});
