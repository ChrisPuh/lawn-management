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


    public function iconPath(): string
    {
        return match ($this) {
            self::MOW => 'M4 14v-4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zM8 8V6m8 2V6m-4 2V6M7 16v2m10-2v2M10 12h4',
            self::WATER => 'M12 3l4.5 4.5c2.5 2.5 2.5 6.5 0 9s-6.5 2.5-9 0-2.5-6.5 0-9L12 3zM8 13.5c2-1.5 6-1.5 8 0',
            self::FERTILIZE => 'M7 8h10l2 3v5a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-5l2-3zM12 8v8m-3-4.5l6 1m-6 0c.5-2 5.5-2 6 0',
            default => 'M12 4v16m8-8H4',
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

    public function pastTense(): string
    {
        return match ($this) {
            self::MOW => 'gemäht',
            self::FERTILIZE => 'gedüngt',
            self::WATER => 'bewässert',
            self::AERATE => 'gelüftet',
            self::SCARIFY => 'vertikutiert',
            self::OVERSEED => 'nachgesät',
            self::WEED => 'Unkraut entfernt',
            self::PEST_CONTROL => 'Schädlinge bekämpft',
            self::SOIL_TEST => 'Boden analysiert',
            self::LIME => 'gekalkt',
            self::LEAF_REMOVAL => 'Laub entfernt',
        };
    }

    public function actionLabel(): string
    {
        return match ($this) {
            self::MOW => 'Ich war mähen',
            self::FERTILIZE => 'Ich habe gedüngt',
            self::WATER => 'Ich habe bewässert',
            self::AERATE => 'Ich habe gelüftet',
            self::SCARIFY => 'Ich habe vertikutiert',
            self::OVERSEED => 'Ich habe nachgesät',
            self::WEED => 'Ich habe Unkraut entfernt',
            self::PEST_CONTROL => 'Ich habe Schädlinge bekämpft',
            self::SOIL_TEST => 'Ich habe den Boden analysiert',
            self::LIME => 'Ich habe gekalkt',
            self::LEAF_REMOVAL => 'Ich habe Laub entfernt',
        };
    }

    public function formLabel(): string
    {
        return match ($this) {
            self::MOW => 'mähen',
            self::FERTILIZE => 'düngen',
            self::WATER => 'bewässern',
            self::AERATE => 'lüften',
            self::SCARIFY => 'vertikutieren',
            self::OVERSEED => 'nachsähen',
            self::WEED => 'Unkraut entfernen',
            self::PEST_CONTROL => 'Schädlinge bekämpfen',
            self::SOIL_TEST => 'Boden analysieren',
            self::LIME => 'kalken',
            self::LEAF_REMOVAL => 'Laub entfernen',


        };
    }
}
