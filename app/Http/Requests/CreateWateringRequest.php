<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class CreateWateringRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'lawn_id' => ['required', 'exists:lawns,id'],
            'height_mm' => ['required', 'numeric', 'min:20', 'max:100'],

            'amount_liters' => ['required', 'numeric', 'min:0.01', 'max:1000'],
            'duration_minutes' => ['required', 'numeric', 'min:15', 'max:120'],
            'method' => ['required', new Enum(WateringMethod::class)],
            'temperature_celsius' => ['nullable', 'numeric', 'min:-20', 'max:50'],
            'weather_condition' => ['nullable', new Enum(WeatherCondition::class)],
            'time_of_day' => ['nullable', new Enum(TimeOfDay::class)],

            'notes' => ['nullable', 'string', 'max:1000'],
            'performed_at' => ['nullable', 'date'],
            'scheduled_for' => ['nullable', 'date', 'after:now'],
        ];
    }
}
