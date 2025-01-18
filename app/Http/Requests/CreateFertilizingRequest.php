<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateFertilizingRequest extends FormRequest
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

            'product_name' => ['required', 'string', 'max:255'],
            'amount_per_sqm' => ['required', 'numeric', 'min:0.01', 'max:1000'],
            'nutrients' => ['required', 'string', 'max:255'],
            'watered' => ['boolean'],
            'temperature_celsius' => ['nullable', 'numeric', 'min:-20', 'max:50'],
            'weather_condition' => ['nullable', 'string', 'max:255'],

            'notes' => ['nullable', 'string', 'max:1000'],
            'performed_at' => ['nullable', 'date'],
            'scheduled_for' => ['nullable', 'date', 'after:now'],
        ];
    }
}
