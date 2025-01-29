<?php

declare(strict_types=1);

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Rules\Validation\LawnRules;

describe('LawnRules', function (): void {
    describe('nameRules', function (): void {
        it('returns expected validation rules', function (): void {
            $rules = LawnRules::nameRules();

            expect($rules)->toBeArray()
                ->toHaveLength(6)
                ->toContain('required')
                ->toContain('string')
                ->toContain('min:3')
                ->toContain('max:255')
                ->toContain('regex:/^[a-zA-Z0-9\s\-_äöüÄÖÜß]+$/');
        });
    });

    describe('locationRules', function (): void {
        it('returns expected validation rules', function (): void {
            $rules = LawnRules::locationRules();

            expect($rules)->toBeArray()
                ->toHaveLength(4)
                ->toContain('nullable')
                ->toContain('string')
                ->toContain('max:255')
                ->toContain('regex:/^[a-zA-Z0-9\s\-_äöüÄÖÜß]+$/');
        });
    });

    describe('sizeRules', function (): void {
        it('returns expected validation rules', function (): void {
            $rules = LawnRules::sizeRules();

            expect($rules)->toBeArray()
                ->toHaveLength(4)
                ->toContain('nullable')
                ->toContain('string')
                ->toContain('max:255')
                ->toContain('regex:/^[0-9,.\s]+m²$/');
        });
    });

    describe('grassSeedRules', function (): void {
        it('contains all enum values', function (): void {
            $rules = LawnRules::grassSeedRules();
            $enumValues = collect(GrassSeed::cases())->map->value()->implode(',');

            expect($rules)->toBeArray()
                ->toHaveLength(3)
                ->toContain('nullable')
                ->toContain('string')
                ->toContain("in:$enumValues");
        });
    });

    describe('typeRules', function (): void {
        it('contains all enum values', function (): void {
            $rules = LawnRules::typeRules();
            $enumValues = collect(GrassType::cases())->map->value()->implode(',');

            expect($rules)->toBeArray()
                ->toHaveLength(3)
                ->toContain('nullable')
                ->toContain('string')
                ->toContain("in:$enumValues");
        });
    });

    describe('messages', function (): void {
        it('returns all required validation messages', function (): void {
            $messages = LawnRules::messages();

            expect($messages)->toBeArray()
                ->toHaveKey('name.required')
                ->toHaveKey('name.min')
                ->toHaveKey('name.max')
                ->toHaveKey('name.regex')
                ->toHaveKey('name.unique')
                ->toHaveKey('location.max')
                ->toHaveKey('location.regex')
                ->toHaveKey('size.max')
                ->toHaveKey('size.regex')
                ->toHaveKey('grass_seed.in')
                ->toHaveKey('type.in');
        });
    });
});
