<?php

declare(strict_types=1);

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Rules\Validation\LawnRules;

describe('LawnRules', function () {
    describe('nameRules', function () {
        it('returns expected validation rules', function () {
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

    describe('locationRules', function () {
        it('returns expected validation rules', function () {
            $rules = LawnRules::locationRules();

            expect($rules)->toBeArray()
                ->toHaveLength(4)
                ->toContain('nullable')
                ->toContain('string')
                ->toContain('max:255')
                ->toContain('regex:/^[a-zA-Z0-9\s\-_äöüÄÖÜß]+$/');
        });
    });

    describe('sizeRules', function () {
        it('returns expected validation rules', function () {
            $rules = LawnRules::sizeRules();

            expect($rules)->toBeArray()
                ->toHaveLength(4)
                ->toContain('nullable')
                ->toContain('string')
                ->toContain('max:255')
                ->toContain('regex:/^[0-9,.\s]+m²$/');
        });
    });

    describe('grassSeedRules', function () {
        it('contains all enum values', function () {
            $rules = LawnRules::grassSeedRules();
            $enumValues = collect(GrassSeed::cases())->map->value()->implode(',');

            expect($rules)->toBeArray()
                ->toHaveLength(3)
                ->toContain('nullable')
                ->toContain('string')
                ->toContain("in:$enumValues");
        });
    });

    describe('typeRules', function () {
        it('contains all enum values', function () {
            $rules = LawnRules::typeRules();
            $enumValues = collect(GrassType::cases())->map->value()->implode(',');

            expect($rules)->toBeArray()
                ->toHaveLength(3)
                ->toContain('nullable')
                ->toContain('string')
                ->toContain("in:$enumValues");
        });
    });

    describe('messages', function () {
        it('returns all required validation messages', function () {
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
