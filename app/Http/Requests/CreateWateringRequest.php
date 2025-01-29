<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;
use Illuminate\Validation\Rules\Enum;

final class CreateWateringRequest extends BaseLawnCareRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'amount_liters' => ['required', 'numeric', 'min:0.01', 'max:1000'],
            'duration_minutes' => ['required', 'numeric', 'min:15', 'max:120'],
            'method' => ['required', new Enum(WateringMethod::class)],
            'temperature_celsius' => ['nullable', 'numeric', 'min:-20', 'max:50'],
            'weather_condition' => ['nullable', new Enum(WeatherCondition::class)],
            'time_of_day' => ['nullable', new Enum(TimeOfDay::class)],
        ];
    }
}
