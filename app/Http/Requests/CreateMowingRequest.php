<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\MowingPattern;
use Illuminate\Validation\Rules\Enum;

final class CreateMowingRequest extends BaseLawnCareRequest
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
            'height_mm' => ['required', 'numeric', 'min:20', 'max:100'],
            'pattern' => ['nullable', new Enum(MowingPattern::class)],
            'collected' => ['boolean'],
            'blade_condition' => ['nullable', new Enum(BladeCondition::class)],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
