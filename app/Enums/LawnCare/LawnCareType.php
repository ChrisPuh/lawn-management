<?php

declare(strict_types=1);

namespace App\Enums\LawnCare;

enum LawnCareType: string
{
    case MOW = 'mow';
    case FERTILIZE = 'fertilize';
    case WATER = 'water';
    case AERATE = 'aerate';
    case SCARIFY = 'scarify';  // Vertikutieren
    case OVERSEED = 'overseed';
    case WEED = 'weed';
    case PEST_CONTROL = 'pest_control';
    case SOIL_TEST = 'soil_test';
    case LIME = 'lime';        // Kalken
    case LEAF_REMOVAL = 'leaf_removal';

    public function label(): string
    {
        return match ($this) {
            self::MOW => 'Mähen',
            self::FERTILIZE => 'Düngen',
            self::WATER => 'Bewässern',
            self::AERATE => 'Lüften',
            self::SCARIFY => 'Vertikutieren',
            self::OVERSEED => 'Nachsäen',
            self::WEED => 'Unkrautbekämpfung',
            self::PEST_CONTROL => 'Schädlingsbekämpfung',
            self::SOIL_TEST => 'Bodenanalyse',
            self::LIME => 'Kalken',
            self::LEAF_REMOVAL => 'Laub entfernen',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::MOW => 'mower',
            self::FERTILIZE => 'seed',
            self::WATER => 'droplet',
            self::AERATE => 'air-vent',
            self::SCARIFY => 'scissors',
            self::OVERSEED => 'seed',
            self::WEED => 'weed',
            self::PEST_CONTROL => 'bug',
            self::SOIL_TEST => 'microscope',
            self::LIME => 'spray',
            self::LEAF_REMOVAL => 'leaf',
        };
    }

    public function requiresScheduling(): bool
    {
        return match ($this) {
            self::FERTILIZE,
            self::OVERSEED,
            self::SCARIFY,
            self::LIME => true,
            default => false,
        };
    }

    public function recommendedInterval(): ?int
    {
        return match ($this) {
            self::MOW => 7,          // alle 7 Tage
            self::FERTILIZE => 90,    // alle 90 Tage
            self::SCARIFY => 180,     // 2x pro Jahr
            self::SOIL_TEST => 365,   // 1x pro Jahr
            self::LIME => 365,        // 1x pro Jahr
            default => null,
        };
    }

    public function seasonalConstraints(): array
    {
        return match ($this) {
            self::SCARIFY => ['spring', 'fall'],
            self::OVERSEED => ['spring', 'fall'],
            self::FERTILIZE => ['spring', 'summer', 'fall'],
            self::LIME => ['fall', 'winter'],
            default => ['spring', 'summer', 'fall', 'winter'],
        };
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function requiredDataFields(): array
    {
        return match ($this) {
            self::MOW => [
                'height_mm' => ['type' => 'number', 'required' => true],
                'pattern' => ['type' => 'select', 'required' => false],
                'collected' => ['type' => 'boolean', 'required' => false],
                'blade_condition' => ['type' => 'select', 'required' => false],
                'duration_minutes' => ['type' => 'number', 'required' => false],
            ],
            self::FERTILIZE => [
                'product_name' => ['type' => 'text', 'required' => true],
                'amount_per_sqm' => ['type' => 'number', 'required' => true],
                'nutrients' => ['type' => 'object', 'required' => true],
                'watered' => ['type' => 'boolean', 'required' => false],
                'temperature_celsius' => ['type' => 'number', 'required' => false],
                'weather_condition' => ['type' => 'select', 'required' => false],
            ],
            self::WATER => [
                'amount_liters' => ['type' => 'number', 'required' => true],
                'duration_minutes' => ['type' => 'number', 'required' => true],
                'method' => ['type' => 'select', 'required' => true],
                'temperature_celsius' => ['type' => 'number', 'required' => false],
                'weather_condition' => ['type' => 'select', 'required' => false],
                'time_of_day' => ['type' => 'select', 'required' => false],
            ],
            default => [],
        };
    }
}
