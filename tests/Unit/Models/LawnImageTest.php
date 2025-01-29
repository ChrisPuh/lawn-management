<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Lawn;
use App\Models\LawnImage;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

describe('LawnImage Model', function (): void {
    describe('attributes', function (): void {
        test('has correct fillable attributes', function (): void {
            $image = new LawnImage;

            expect($image->getFillable())->toBe([
                'lawn_id',
                'image_path',
                'imageable_id',
                'imageable_type',
                'type',
                'description',
                'archived_at',
                'delete_after',
            ]);
        });

        test('converts to array with correct keys', function (): void {
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
                'archived_at',
                'delete_after',
                'created_at',
                'updated_at',
            ]);
        });

        test('casts datetime fields correctly', function (): void {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->create([
                'archived_at' => now(),
                'delete_after' => now()->addMonths(3),
            ]);

            expect($image->archived_at)->toBeInstanceOf(Carbon::class);
            expect($image->delete_after)->toBeInstanceOf(Carbon::class);
        });

        test('uses correct table name', function (): void {
            $image = new LawnImage;

            expect($image->getTable())->toBe('lawn_images');
        });
    });

    describe('relationships', function (): void {
        test('belongs to lawn', function (): void {
            $lawn = Lawn::factory()->create();
            /** @var LawnImage $image */
            $image = LawnImage::factory()->create(['lawn_id' => $lawn->id]);

            expect($image->lawn)->toBeInstanceOf(Lawn::class)
                ->and($image->lawn->id)->toBe($lawn->id);
        });

        describe('polymorphic relationships', function (): void {
            test('can be associated with lawn', function (): void {
                $mowing = Lawn::factory()->create();
                /** @var LawnImage $image */
                $image = LawnImage::factory()->create([
                    'imageable_id' => $mowing->id,
                    'imageable_type' => Lawn::class,
                ]);

                expect($image->imageable)->toBeInstanceOf(Lawn::class)
                    ->and($image->imageable->id)->toBe($mowing->id);
            });

        });
    });

    describe('factory', function (): void {
        test('can create image record using factory', function (): void {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->create();

            expect($image)->toBeInstanceOf(LawnImage::class)
                ->and($image->exists)->toBeTrue()
                ->and($image->lawn)->toBeInstanceOf(Lawn::class);
        });
    });

    describe('validation', function (): void {
        test('requires lawn_id to be present', function (): void {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->make(['lawn_id' => null]);

            expect(fn () => $image->save())
                ->toThrow(QueryException::class);
        });

        test('allows image_path to be null', function (): void {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->make(['image_path' => null]);

            expect($image->image_path)->toBeNull();

        });

        test('requires imageable_id to be present', function (): void {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->make(['imageable_id' => null]);

            expect(fn () => $image->save())
                ->toThrow(QueryException::class);
        });

        test('requires imageable_type to be present', function (): void {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->make(['imageable_type' => null]);

            expect(fn () => $image->save())
                ->toThrow(QueryException::class);
        });

        test('requires type to be present', function (): void {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->make(['type' => null]);

            expect(fn () => $image->save())
                ->toThrow(QueryException::class);
        });

        test('allows description to be null', function (): void {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->create(['description' => null]);

            expect($image->description)->toBeNull();
        });

        test('allows archived_at to be null', function (): void {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->create(['archived_at' => null]);

            expect($image->archived_at)->toBeNull();
        });

        test('allows delete_after to be null', function (): void {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->create(['delete_after' => null]);

            expect($image->delete_after)->toBeNull();
        });
    });

    describe('archiving', function (): void {
        test('can archive an image', function (): void {
            /** @var LawnImage $image */
            $image = LawnImage::factory()->create();

            $archivedAt = now();
            $deleteAfter = now()->addMonths(3);

            $image->update([
                'archived_at' => $archivedAt,
                'delete_after' => $deleteAfter,
            ]);

            expect($image->archived_at->timestamp)->toBe($archivedAt->timestamp)
                ->and($image->delete_after->timestamp)->toBe($deleteAfter->timestamp);
        });

        test('can query archived images', function (): void {
            // Create some archived and non-archived images
            LawnImage::factory()->count(3)->create(['archived_at' => null]);
            LawnImage::factory()->count(2)->create(['archived_at' => now()]);

            $archivedCount = LawnImage::whereNotNull('archived_at')->count();
            $nonArchivedCount = LawnImage::whereNull('archived_at')->count();

            expect($archivedCount)->toBe(2)
                ->and($nonArchivedCount)->toBe(3);
        });

        test('can query images pending deletion', function (): void {
            // Create images with different delete_after dates
            LawnImage::factory()->count(2)->create([
                'archived_at' => now(),
                'delete_after' => now()->subDay(),
            ]);
            LawnImage::factory()->count(3)->create([
                'archived_at' => now(),
                'delete_after' => now()->addDay(),
            ]);

            $pendingDeletionCount = LawnImage::where('delete_after', '<', now())->count();
            expect($pendingDeletionCount)->toBe(2);
        });
    });
});
