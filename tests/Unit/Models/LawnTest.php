<?php

declare(strict_types=1);

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Models\Lawn;
use App\Models\LawnImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Lawn Model', function (): void {
    describe('attributes', function (): void {
        test('has correct fillable attributes', function (): void {
            $lawn = new Lawn;

            expect($lawn->getFillable())->toBe([
                'name',
                'location',
                'size',
                'grass_seed',
                'user_id',
                'type',
            ]);
        });

        test('converts to array with correct keys', function (): void {
            $lawn = Lawn::factory()->create();

            expect(array_keys($lawn->fresh()->toArray()))->toBe([
                'id',
                'name',
                'location',
                'size',
                'grass_seed',
                'type',
                'user_id',
                'created_at',
                'updated_at',
            ]);
        });

        test('uses correct table name', function (): void {
            $lawn = new Lawn;

            expect($lawn->getTable())->toBe('lawns');
        });

        test('has correct attribute casts', function (): void {
            $lawn = new Lawn;
            $casts = $lawn->getCasts();

            // Sort both arrays to ensure consistent order
            ksort($casts);
            $expected = [
                'grass_seed' => GrassSeed::class,
                'type' => GrassType::class,
                'id' => 'int',
            ];
            ksort($expected);

            expect($casts)->toBe($expected);
        });
    });

    describe('relationships', function (): void {
        test('belongs to user', function (): void {
            $user = User::factory()->create();
            $lawn = Lawn::factory()->create(['user_id' => $user->getKey()]);

            expect($lawn->user)->toBeInstanceOf(User::class)
                ->and($lawn->user->getKey())->toBe($user->getKey());
        });

        describe('images', function (): void {
            test('has many images', function (): void {
                $lawn = Lawn::factory()->create();
                LawnImage::factory()->count(5)->create(['lawn_id' => $lawn->getKey()]);

                expect($lawn->images->count())->toBe(5)
                    ->and($lawn->images->first())->toBeInstanceOf(LawnImage::class);
            });
        });
    });

    describe('factory', function (): void {
        test('can create lawn record using factory', function (): void {
            $lawn = Lawn::factory()->create();

            expect($lawn)->toBeInstanceOf(Lawn::class)
                ->and($lawn->exists)->toBeTrue()
                ->and($lawn->user)->toBeInstanceOf(User::class);
        });

        test('can override attributes when creating', function (): void {
            $user = User::factory()->create();
            $customName = 'Test Lawn';
            $customLocation = 'Backyard';
            $customSize = '100m²';
            $customGrassSeed = GrassSeed::FestucaOvina;
            $customType = GrassType::Garden;

            $lawn = Lawn::factory()->create([
                'user_id' => $user->getKey(),
                'name' => $customName,
                'location' => $customLocation,
                'size' => $customSize,
                'grass_seed' => $customGrassSeed,
                'type' => $customType,
            ]);

            expect($lawn->user_id)->toBe($user->getKey())
                ->and($lawn->name)->toBe($customName)
                ->and($lawn->location)->toBe($customLocation)
                ->and($lawn->size)->toBe($customSize)
                ->and($lawn->grass_seed)->toBe($customGrassSeed)
                ->and($lawn->type)->toBe($customType);
        });

        test('creates lawn with nullable fields as null', function (): void {
            $lawn = Lawn::factory()->create([
                'location' => null,
                'size' => null,
                'grass_seed' => null,
                'type' => null,
            ]);

            expect($lawn->location)->toBeNull()
                ->and($lawn->size)->toBeNull()
                ->and($lawn->grass_seed)->toBeNull()
                ->and($lawn->type)->toBeNull();
        });
    });

    describe('validation', function (): void {
        test('requires name to be present', function (): void {
            $lawn = Lawn::factory()->make(['name' => null]);

            expect(fn () => $lawn->save())
                ->toThrow(Illuminate\Database\QueryException::class);
        });

        test('requires user_id to be present', function (): void {
            $lawn = Lawn::factory()->make(['user_id' => null]);

            expect(fn () => $lawn->save())
                ->toThrow(Illuminate\Database\QueryException::class);
        });

        test('allows location to be null', function (): void {
            $lawn = Lawn::factory()->create(['location' => null]);

            expect($lawn->location)->toBeNull();
        });

        test('allows size to be null', function (): void {
            $lawn = Lawn::factory()->create(['size' => null]);

            expect($lawn->size)->toBeNull();
        });

        test('allows grass_seed to be null', function (): void {
            $lawn = Lawn::factory()->create(['grass_seed' => null]);

            expect($lawn->grass_seed)->toBeNull();
        });

        test('allows type to be null', function (): void {
            $lawn = Lawn::factory()->create(['type' => null]);

            expect($lawn->type)->toBeNull();
        });

        test('validates grass_seed is valid enum value', function (): void {
            $lawn = Lawn::factory()->create(['grass_seed' => 'FestucaOvina']);

            expect($lawn->grass_seed)->toBe(GrassSeed::FestucaOvina);
        });

        test('validates type is valid enum value', function (): void {
            $lawn = Lawn::factory()->create(['type' => 'Garden']);

            expect($lawn->type)->toBe(GrassType::Garden);
        });
    });

    describe('scopes', function (): void {
        test('for user scope returns only authenticated user lawns', function (): void {
            // Arrange
            $user = User::factory()->create();
            $otherUser = User::factory()->create();

            $userLawns = Lawn::factory()->count(2)->create(['user_id' => $user->getKey()]);
            Lawn::factory()->count(3)->create(['user_id' => $otherUser->getKey()]);

            // Act
            Auth::login($user);
            $lawns = Lawn::forUser()->get();

            // Assert
            expect($lawns)->toHaveCount(2)
                ->and($lawns->pluck('id')->toArray())
                ->toBe($userLawns->pluck('id')->toArray());

            // Clean up
            Auth::logout();
        });

        test('for user scope returns empty collection when not authenticated', function (): void {
            // Arrange
            $user = User::factory()->create();
            Lawn::factory()->count(2)->create(['user_id' => $user->getKey()]);

            // Act
            Auth::logout();
            $lawns = Lawn::forUser()->get();

            // Assert
            expect($lawns)->toHaveCount(0);
        });
    });
});
