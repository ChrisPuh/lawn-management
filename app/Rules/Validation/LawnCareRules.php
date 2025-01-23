<?php

declare(strict_types=1);

namespace App\Rules\Validation;

use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\LawnCareType;
use App\Enums\LawnCare\MowingPattern;
use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;
use Illuminate\Validation\Rules\Enum;

final class LawnCareRules
{
    public static function commonRules(): array
    {
        return [
            'lawn_id' => ['required', 'exists:lawns,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'performed_at' => ['nullable', 'date'],
            'scheduled_for' => ['nullable', 'date', 'after:now'],
            'completed_at' => ['nullable', 'date'],
        ];
    }

    public static function mowingRules(): array
    {
        return [
            'height_mm' => ['required', 'numeric', 'min:20', 'max:100'],
            'pattern' => ['nullable', new Enum(MowingPattern::class)],
            'collected' => ['boolean'],
            'blade_condition' => ['nullable', new Enum(BladeCondition::class)],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public static function fertilizingRules(): array
    {
        return [
            'product_name' => ['required', 'string', 'max:255'],
            'amount_per_sqm' => ['required', 'numeric', 'min:0.01', 'max:1000'],
            'nutrients' => ['required', 'array'],
            'watered' => ['boolean'],
            'temperature_celsius' => ['nullable', 'numeric', 'min:-20', 'max:50'],
            'weather_condition' => ['nullable', new Enum(WeatherCondition::class)],
        ];
    }

    public static function wateringRules(): array
    {
        return [
            'amount_liters' => ['required', 'numeric', 'min:0.01', 'max:1000'],
            'duration_minutes' => ['required', 'numeric', 'min:15', 'max:120'],
            'method' => ['required', new Enum(WateringMethod::class)],
            'temperature_celsius' => ['nullable', 'numeric', 'min:-20', 'max:50'],
            'weather_condition' => ['nullable', new Enum(WeatherCondition::class)],
            'time_of_day' => ['nullable', new Enum(TimeOfDay::class)],
        ];
    }

    public static function getRulesForType(LawnCareType $type): array
    {
        $rules = self::commonRules();

        $careDataRules = match ($type) {
            LawnCareType::MOW => self::mowingRules(),
            LawnCareType::FERTILIZE => self::fertilizingRules(),
            LawnCareType::WATER => self::wateringRules(),
            default => [],
        };

        return array_merge(
            $rules,
            array_combine(
                array_map(fn ($key) => "care_data.$key", array_keys($careDataRules)),
                array_values($careDataRules)
            )
        );
    }

    public static function messages(): array
    {
        return [
            // Common messages
            'lawn_id.required' => 'Die Rasenfläche ist erforderlich.',
            'lawn_id.exists' => 'Die ausgewählte Rasenfläche existiert nicht.',
            'notes.max' => 'Die Notizen dürfen maximal :max Zeichen lang sein.',

            // Type specific messages...
            'care_data.height_mm.required' => 'Die Schnitthöhe ist erforderlich.',
            'care_data.height_mm.min' => 'Die Schnitthöhe muss mindestens :min mm betragen.',
            'care_data.height_mm.max' => 'Die Schnitthöhe darf maximal :max mm betragen.',
            // Add more messages...
        ];
    }
}
