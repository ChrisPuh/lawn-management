<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\LawnImageType;
use App\Models\Lawn;
use App\Models\LawnAerating;
use App\Models\LawnFertilizing;
use App\Models\LawnImage;
use App\Models\LawnMowing;
use App\Models\LawnScarifying;
use Illuminate\Database\QueryException;

describe('LawnImage Model', function () {
    describe('attributes', function () {
        test('has correct fillable attributes', function () {
            $image = new LawnImage;

            expect($image->getFillable())->toBe([
                'lawn_id',
                'image_path',
                'imageable_id',
                'imageable_type',
                'type',
                'description',
            ]);
        });

        test('converts to array with correct keys', function () {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->create();

            expect(array_keys($image->fresh()->toArray()))->toBe([
                'id',
                'lawn_id',
                'image_path',
                'imageable_type',
                'imageable_id',
                'type',
                'description',
                'created_at',
                'updated_at',
            ]);
        });

        test('uses correct table name', function () {
            $image = new LawnImage;

            expect($image->getTable())->toBe('lawn_images');
        });
    });

    describe('relationships', function () {
        test('belongs to lawn', function () {
            $lawn = Lawn::factory()->create();
            /** @var LawnImage $image */
            $image = LawnImage::factory()->create(['lawn_id' => $lawn->id]);

            expect($image->lawn)->toBeInstanceOf(Lawn::class);
            expect($image->lawn->id)->toBe($lawn->id);
        });

        describe('polymorphic relationships', function () {
            test('can be associated with lawn mowing', function () {
                $mowing = LawnMowing::factory()->create();
                /** @var LawnImage $image */
                $image = LawnImage::factory()->create([
                    'imageable_id' => $mowing->id,
                    'imageable_type' => LawnMowing::class,
                ]);

                expect($image->imageable)->toBeInstanceOf(LawnMowing::class);
                expect($image->imageable->id)->toBe($mowing->id);
            });

            test('can be associated with lawn fertilizing', function () {
                $fertilizing = LawnFertilizing::factory()->create();
                /** @var LawnImage $image */
                $image = LawnImage::factory()->create([
                    'imageable_id' => $fertilizing->id,
                    'imageable_type' => LawnFertilizing::class,
                ]);

                expect($image->imageable)->toBeInstanceOf(LawnFertilizing::class);
                expect($image->imageable->id)->toBe($fertilizing->id);
            });

            test('can be associated with lawn scarifying', function () {
                $scarifying = LawnScarifying::factory()->create();
                /** @var LawnImage $image */
                $image = LawnImage::factory()->create([
                    'imageable_id' => $scarifying->id,
                    'imageable_type' => LawnScarifying::class,
                ]);

                expect($image->imageable)->toBeInstanceOf(LawnScarifying::class);
                expect($image->imageable->id)->toBe($scarifying->id);
            });

            test('can be associated with lawn aerating', function () {
                $aerating = LawnAerating::factory()->create();
                /** @var LawnImage $image */
                $image = LawnImage::factory()->create([
                    'imageable_id' => $aerating->id,
                    'imageable_type' => LawnAerating::class,
                ]);

                expect($image->imageable)->toBeInstanceOf(LawnAerating::class);
                expect($image->imageable->id)->toBe($aerating->id);
            });
        });
    });

    describe('factory', function () {
        test('can create image record using factory', function () {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->create();

            expect($image)->toBeInstanceOf(LawnImage::class);
            expect($image->exists)->toBeTrue();
            expect($image->lawn)->toBeInstanceOf(Lawn::class);
        });

        test('can override attributes when creating', function () {
            $lawn = Lawn::factory()->create();
            $mowing = LawnMowing::factory()->create();
            $customPath = 'images/test.jpg';
            $customDescription = 'Test Description';
            $customType = LawnImageType::AFTER->value;

            /** @var LawnImage $image */
            $image = LawnImage::factory()->create([
                'lawn_id' => $lawn->id,
                'image_path' => $customPath,
                'imageable_id' => $mowing->id,
                'imageable_type' => LawnMowing::class,
                'type' => $customType,
                'description' => $customDescription,
            ]);

            expect($image->lawn_id)->toBe($lawn->id);
            expect($image->image_path)->toBe($customPath);
            expect($image->imageable_id)->toBe($mowing->id);
            expect($image->imageable_type)->toBe(LawnMowing::class);
            expect($image->type->value)->toBe($customType);
            expect($image->description)->toBe($customDescription);
        });
    });

    describe('validation', function () {
        test('requires lawn_id to be present', function () {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->make(['lawn_id' => null]);

            expect(fn () => $image->save())
                ->toThrow(QueryException::class);
        });

        test('requires image_path to be present', function () {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->make(['image_path' => null]);

            expect(fn () => $image->save())
                ->toThrow(QueryException::class);
        });

        test('requires imageable_id to be present', function () {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->make(['imageable_id' => null]);

            expect(fn () => $image->save())
                ->toThrow(QueryException::class);
        });

        test('requires imageable_type to be present', function () {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->make(['imageable_type' => null]);

            expect(fn () => $image->save())
                ->toThrow(QueryException::class);
        });

        test('requires type to be present', function () {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->make(['type' => null]);

            expect(fn () => $image->save())
                ->toThrow(QueryException::class);
        });

        test('allows description to be null', function () {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->create(['description' => null]);

            expect($image->description)->toBeNull();
        });
    });
});
