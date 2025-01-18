<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\MowingPattern;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class CreateMowingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
            'pattern' => ['nullable', new Enum(MowingPattern::class)],
            'collected' => ['boolean'],
            'blade_condition' => ['nullable', new Enum(BladeCondition::class)],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'performed_at' => ['nullable', 'date'],
            'scheduled_for' => ['nullable', 'date', 'after:now'],
        ];
    }
}
