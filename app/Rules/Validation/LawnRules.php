<?php

declare(strict_types=1);

namespace App\Rules\Validation;

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class LawnRules
{
    public static function nameRules(?int $ignoreLawnId = null): array
    {
        return [
            'required',
            'string',
            'min:3',
            'max:255',
            'regex:/^[a-zA-Z0-9\s\-_äöüÄÖÜß]+$/',
            Rule::unique('lawns', 'name')
                ->where(fn ($query) => $query->where('user_id', Auth::id()))
                ->when($ignoreLawnId, fn ($rule) => $rule->ignore($ignoreLawnId)),
        ];
    }

    public static function locationRules(): array
    {
        return [
            'nullable',
            'string',
            'max:255',
            'regex:/^[a-zA-Z0-9\s\-_äöüÄÖÜß]+$/',
        ];
    }

    public static function sizeRules(): array
    {
        return [
            'nullable',
            'string',
            'max:255',
            'regex:/^[0-9,.\s]+m²$/',
        ];
    }

    public static function grassSeedRules(): array
    {
        return [
            'nullable',
            'string',
            'in:'.collect(GrassSeed::cases())->map->value()->implode(','),
        ];
    }

    public static function typeRules(): array
    {
        return [
            'nullable',
            'string',
            'in:'.collect(GrassType::cases())->map->value()->implode(','),
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'Bitte geben Sie einen Namen ein.',
            'name.min' => 'Der Name muss mindestens :min Zeichen lang sein.',
            'name.max' => 'Der Name darf maximal :max Zeichen lang sein.',
            'name.regex' => 'Der Name enthält unerlaubte Zeichen.',
            'name.unique' => 'Eine Rasenfläche mit diesem Namen existiert bereits.',
            'location.max' => 'Der Standort darf maximal :max Zeichen lang sein.',
            'location.regex' => 'Der Standort enthält unerlaubte Zeichen.',
            'size.max' => 'Die Größe darf maximal :max Zeichen lang sein.',
            'size.regex' => 'Bitte geben Sie eine gültige Größe an (z.B. 100m²).',
            'grass_seed.in' => 'Bitte wählen Sie eine gültige Grassorte.',
            'type.in' => 'Bitte wählen Sie einen gültigen Rasentyp.',
        ];
    }
}
