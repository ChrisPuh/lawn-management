<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\LawnCareType;
use App\Enums\LawnCare\MowingPattern;
use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;
use Illuminate\Validation\Rules\Enum;

final class LawnCareRules
{
    public static function getRules(LawnCareType $type): array
    {
        $commonRules = [
            'lawn_id' => ['required', 'exists:lawns,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'performed_at' => ['nullable', 'date'],
            'scheduled_for' => ['nullable', 'date', 'after:now'],
        ];

        $specificRules = match ($type) {
            LawnCareType::MOW => [
                'care_data.height_mm' => ['required', 'numeric', 'min:1', 'max:100'],
                'care_data.pattern' => ['nullable', new Enum(MowingPattern::class)],
                'care_data.collected' => ['boolean'],
                'care_data.blade_condition' => ['nullable', new Enum(BladeCondition::class)],
                'care_data.duration_minutes' => ['nullable', 'integer', 'min:1'],
            ],
            LawnCareType::FERTILIZE => [
                'care_data.product_name' => ['required', 'string', 'max:255'],
                'care_data.amount_per_sqm' => ['required', 'numeric', 'min:0.01', 'max:1000'],
                'care_data.nutrients' => ['required', 'array'],
                'care_data.watered' => ['boolean'],
                'care_data.temperature_celsius' => ['nullable', 'numeric', 'min:-20', 'max:50'],
                'care_data.weather_condition' => ['nullable', new Enum(WeatherCondition::class)],
            ],
            LawnCareType::WATER => [
                'care_data.amount_liters' => ['required', 'numeric', 'min:0.01', 'max:1000'],
                'care_data.duration_minutes' => ['required', 'numeric', 'min:15', 'max:120'],
                'care_data.method' => ['required', new Enum(WateringMethod::class)],
                'care_data.temperature_celsius' => ['nullable', 'numeric', 'min:-20', 'max:50'],
                'care_data.weather_condition' => ['nullable', new Enum(WeatherCondition::class)],
                'care_data.time_of_day' => ['nullable', new Enum(TimeOfDay::class)],
            ],
            default => [],
        };

        return array_merge($commonRules, $specificRules);
    }

    public static function getMessages(): array
    {
        return [
            'lawn_id.required' => 'Die Rasenfläche ist erforderlich.',
            'lawn_id.exists' => 'Die ausgewählte Rasenfläche existiert nicht.',
            'notes.max' => 'Die Notizen dürfen maximal :max Zeichen lang sein.',
            'performed_at.date' => 'Das Durchführungsdatum muss ein gültiges Datum sein.',
            'scheduled_for.date' => 'Das Planungsdatum muss ein gültiges Datum sein.',
            'scheduled_for.after' => 'Das Planungsdatum muss in der Zukunft liegen.',

            // Mowing messages
            'care_data.height_mm.required' => 'Die Schnitthöhe ist erforderlich.',
            'care_data.height_mm.min' => 'Die Schnitthöhe muss mindestens :min mm betragen.',
            'care_data.height_mm.max' => 'Die Schnitthöhe darf maximal :max mm betragen.',
            'care_data.duration_minutes.min' => 'Die Dauer muss mindestens :min Minute(n) betragen.',

            // Fertilizing messages
            'care_data.product_name.required' => 'Der Produktname ist erforderlich.',
            'care_data.amount_per_sqm.required' => 'Die Menge pro m² ist erforderlich.',
            'care_data.amount_per_sqm.min' => 'Die Menge pro m² muss mindestens :min betragen.',
            'care_data.amount_per_sqm.max' => 'Die Menge pro m² darf maximal :max betragen.',
            'care_data.nutrients.required' => 'Die Nährstoffe sind erforderlich.',

            // Watering messages
            'care_data.amount_liters.required' => 'Die Wassermenge ist erforderlich.',
            'care_data.amount_liters.min' => 'Die Wassermenge muss mindestens :min Liter betragen.',
            'care_data.amount_liters.max' => 'Die Wassermenge darf maximal :max Liter betragen.',
            'care_data.duration_minutes.required' => 'Die Bewässerungsdauer ist erforderlich.',
            'care_data.method.required' => 'Die Bewässerungsmethode ist erforderlich.',
        ];
    }
}
