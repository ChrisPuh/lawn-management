<?php

declare(strict_types=1);

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Models\Lawn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);

describe('Lawn Model', function () {
    // Rest des Codes bleibt gleich bis zu den Validierungstests...

    describe('validation', function () {
        test('requires name to be present', function () {
            $lawn = Lawn::factory()->make(['name' => null]);

            expect(fn () => $lawn->save())
                ->toThrow(Illuminate\Database\QueryException::class);
        });

        test('requires user_id to be present', function () {
            $lawn = Lawn::factory()->make(['user_id' => null]);

            expect(fn () => $lawn->save())
                ->toThrow(Illuminate\Database\QueryException::class);
        });

        test('allows location to be null', function () {
            $lawn = Lawn::factory()->create(['location' => null]);

            expect($lawn->location)->toBeNull();
        });

        test('allows size to be null', function () {
            $lawn = Lawn::factory()->create(['size' => null]);

            expect($lawn->size)->toBeNull();
        });

        test('allows grass_seed to be null', function () {
            $lawn = Lawn::factory()->create(['grass_seed' => null]);

            expect($lawn->grass_seed)->toBeNull();
        });

        test('allows type to be null', function () {
            $lawn = Lawn::factory()->create(['type' => null]);

            expect($lawn->type)->toBeNull();
        });

        test('validates grass_seed is valid enum value', function () {
            $lawn = Lawn::factory()->create(['grass_seed' => 'FestucaOvina']);

            expect($lawn->grass_seed)->toBe(GrassSeed::FestucaOvina);
        });

        test('validates type is valid enum value', function () {
            $lawn = Lawn::factory()->create(['type' => 'Garden']);

            expect($lawn->type)->toBe(GrassType::Garden);
        });
    });

    describe('scopes', function () {
        test('for user scope returns only authenticated user lawns', function () {
            // Arrange
            $user = User::factory()->create()->first();
            $otherUser = User::factory()->create()->first();

            $userLawns = Lawn::factory()->count(2)->create(['user_id' => $user->getKey()]);
            Lawn::factory()->count(3)->create(['user_id' => $otherUser->getKey()]);

            /**
             * @var Illuminate\Contracts\Auth\Authenticatable $user
             * @var TestCase $this
             */
            $this->actingAs($user);

            // Act
            $lawns = Lawn::forUser()->get();

            // Assert
            expect($lawns)->toHaveCount(2);
            expect($lawns->pluck('id')->toArray())
                ->toBe($userLawns->pluck('id')->toArray());
        });
    });
});
