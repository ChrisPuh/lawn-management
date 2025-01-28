<?php

declare(strict_types=1);

namespace App\Livewire\LawnCare;

use App\Contracts\LawnCare\CreateLawnCareActionContract;
use App\DataObjects\LawnCare\CreateMowingData;
use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\LawnCareType;
use App\Enums\LawnCare\MowingPattern;
use App\Http\Requests\CreateMowingRequest;
use App\Models\Lawn;
use DateMalformedStringException;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class CreateMowing extends Component
{
    public ?int $lawn_id = null;

    public ?float $height_mm = null;

    public ?string $pattern = null;

    public bool $collected = true;

    public ?string $blade_condition = null;

    public ?int $duration_minutes = null;

    public ?string $notes = null;

    public ?string $performed_at = null;

    public ?string $scheduled_for = null;

    public function mount(Lawn $lawn): void
    {
        $this->lawn_id = $lawn->id;
        $this->performed_at = now()->format('Y-m-d H:i');
    }

    /**
     * @throws DateMalformedStringException
     */
    public function save(CreateLawnCareActionContract $action): void
    {
        $validated = $this->validate((new CreateMowingRequest)->rules());

        $action->execute(
            type: LawnCareType::MOW,
            data: CreateMowingData::fromArray(
                validatedData: [
                    'lawn_id' => $validated->lawn_id,
                    'height_mm' => $validated->height_mm,
                    'pattern' => $validated->pattern,
                    'collected' => $validated->collected,
                    'blade_condition' => $validated->blade_condition,
                    'duration_minutes' => $validated->duration_minutes,
                    'notes' => $validated->notes,
                    'performed_at' => $validated->performed_at,
                    'scheduled_for' => $validated->scheduled_for,
                ],
                userId: Auth()->id()

            ),
        );

        $this->dispatch('lawn-care-created');
    }

    public function render(): View
    {
        return view('livewire.lawn-care.create-mowing', [
            'patterns' => MowingPattern::cases(),
            'bladeConditions' => BladeCondition::cases(),
        ]);
    }
}
