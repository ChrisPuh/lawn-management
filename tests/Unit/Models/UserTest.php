<?php

declare(strict_types=1);

use App\Models\Lawn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

describe('User Model', function (): void {
    describe('attributes', function (): void {
        test('has correct fillable attributes', function (): void {
            $user = new User;

            expect($user->getFillable())->toBe([
                'name',
                'email',
                'password',
            ]);
        });

        test('has correct hidden attributes', function (): void {
            $user = new User;

            expect($user->getHidden())->toBe([
                'password',
                'remember_token',
            ]);
        });

        test('uses correct table name', function (): void {
            $user = new User;

            expect($user->getTable())->toBe('users');
        });

        test('has correct attribute casts', function (): void {
            $user = new User;
            $casts = $user->getCasts();

            // Sort both arrays to ensure consistent order
            ksort($casts);
            $expected = [
                'email_verified_at' => 'datetime',
                'password' => 'hashed',
                'id' => 'int',
            ];
            ksort($expected);

            expect($casts)->toBe($expected);
        });

        test('casts email_verified_at to Carbon instance', function (): void {
            $date = '2024-01-15 10:00:00';
            $user = User::factory()->create([
                'email_verified_at' => $date,
            ]);

            expect($user->email_verified_at)->toBeInstanceOf(Carbon::class);
            expect($user->email_verified_at->format('Y-m-d H:i:s'))->toBe($date);
        });

        test('hashes password attribute', function (): void {
            $password = 'secret123';
            $user = User::factory()->create([
                'password' => $password,
            ]);

            expect($user->password)->not->toBe($password);
            expect(Hash::check($password, $user->password))->toBeTrue();
        });
    });

    describe('relationships', function (): void {
        test('has many lawns', function (): void {
            $user = User::factory()->create();
            $lawns = Lawn::factory()->count(3)->create(['user_id' => $user->getKey()]);

            expect($user->lawns->count())->toBe(3);
            expect($user->lawns->first())->toBeInstanceOf(Lawn::class);
        });

        test('cascade deletes related lawns when user is deleted', function (): void {
            $user = User::factory()->create();
            $lawns = Lawn::factory()->count(3)->create(['user_id' => $user->getKey()]);

            $lawnIds = $lawns->pluck('id')->toArray();

            $user->delete();

            expect(Lawn::whereIn('id', $lawnIds)->count())->toBe(0);
        });
    });

    describe('factory', function (): void {
        test('can create user using factory', function (): void {
            $user = User::factory()->create();

            expect($user)->toBeInstanceOf(User::class);
            expect($user->exists)->toBeTrue();
            expect($user->email_verified_at)->not->toBeNull();
        });

        test('can create unverified user', function (): void {
            $user = User::factory()->unverified()->create();

            expect($user->email_verified_at)->toBeNull();
        });

        test('can override attributes when creating', function (): void {
            $name = 'John Doe';
            $email = 'john@example.com';

            $user = User::factory()->create([
                'name' => $name,
                'email' => $email,
            ]);

            expect($user->name)->toBe($name);
            expect($user->email)->toBe($email);
        });
    });

    describe('validation', function (): void {
        test('requires name to be present', function (): void {
            $user = User::factory()->make(['name' => null]);

            expect(fn () => $user->save())
                ->toThrow(Illuminate\Database\QueryException::class);
        });

        test('requires email to be present', function (): void {
            $user = User::factory()->make(['email' => null]);

            expect(fn () => $user->save())
                ->toThrow(Illuminate\Database\QueryException::class);
        });

        test('requires email to be unique', function (): void {
            $email = 'test@example.com';
            User::factory()->create(['email' => $email]);

            expect(fn () => User::factory()->create(['email' => $email]))
                ->toThrow(Illuminate\Database\QueryException::class);
        });

        test('requires password to be present', function (): void {
            $user = User::factory()->make(['password' => null]);

            expect(fn () => $user->save())
                ->toThrow(Illuminate\Database\QueryException::class);
        });

        test('allows email_verified_at to be null', function (): void {
            $user = User::factory()->unverified()->create();

            expect($user->email_verified_at)->toBeNull();
        });

        test('allows remember_token to be null', function (): void {
            $user = User::factory()->create(['remember_token' => null]);

            expect($user->remember_token)->toBeNull();
        });
    });

    describe('email verification', function (): void {
        test('implements MustVerifyEmail interface', function (): void {
            $user = new User;

            expect($user)->toBeInstanceOf(Illuminate\Contracts\Auth\MustVerifyEmail::class);
        });

        test('can mark email as verified', function (): void {
            $user = User::factory()->unverified()->create();

            expect($user->email_verified_at)->toBeNull();

            $user->markEmailAsVerified();

            expect($user->email_verified_at)->not->toBeNull();
            expect($user->hasVerifiedEmail())->toBeTrue();
        });

        test('can check if email is verified', function (): void {
            $verifiedUser = User::factory()->create();
            $unverifiedUser = User::factory()->unverified()->create();

            expect($verifiedUser->hasVerifiedEmail())->toBeTrue();
            expect($unverifiedUser->hasVerifiedEmail())->toBeFalse();
        });
    });
});
