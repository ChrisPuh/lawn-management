<?php

declare(strict_types=1);

namespace App\Livewire\LawnCare;

use App\Actions\LawnCare\CreateMowingAction;
use App\DataObjects\LawnCare\CreateMowingData;
use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\MowingPattern;
use App\Http\Requests\CreateMowingRequest;
use App\Models\Lawn;
use DateMalformedStringException;
use DateTime;
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
    public function save(CreateMowingAction $action): void
    {
        $validated = $this->validate((new CreateMowingRequest)->rules());

        $data = new CreateMowingData(
            lawn_id: $this->lawn_id,
            user_id: auth()->id(),
            height_mm: (float) $this->height_mm,
            pattern: $this->pattern ? MowingPattern::from($this->pattern) : null,
            collected: $this->collected,
            blade_condition: $this->blade_condition ? BladeCondition::from($this->blade_condition) : null,
            duration_minutes: $this->duration_minutes,
            notes: $this->notes,
            performed_at: $this->performed_at ? new DateTime($this->performed_at) : null,
            scheduled_for: $this->scheduled_for ? new DateTime($this->scheduled_for) : null,
        );

        $action->execute($data);

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
