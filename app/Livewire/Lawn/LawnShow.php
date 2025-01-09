<?php

declare(strict_types=1);

namespace App\Livewire\Lawn;

use App\Models\Lawn;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

final class LawnShow extends Component
{
    use WithFileUploads;

    public Lawn $lawn;

    public ?string $lastMowingDate = null;

    public ?string $lastFertilizingDate = null;

    public ?string $lastScarifyingDate = null;

    public ?string $lastAeratingDate = null;

    /**
     * Mounts the component and loads related data
     */
    public function mount(): void
    {
        $this->authorize('view', $this->lawn);

        // Load last maintenance dates
        $this->lastMowingDate = $this->lawn->getLastMowingDate();
        $this->lastFertilizingDate = $this->lawn->getLastFertilizingDate();
        $this->lastScarifyingDate = $this->lawn->getLastScarifyingDate();
        $this->lastAeratingDate = $this->lawn->getLastAeratingDate();
    }

    /**
     * Deletes the lawn and redirects to the lawn index
     */
    #[On('delete-confirmed')]
    public function deleteLawn(): void
    {
        $this->authorize('delete', $this->lawn);

        $this->lawn->delete();
        $this->redirect(route('lawn.index'), navigate: true);
    }

    #[Layout('components.layouts.authenticated.index', ['title' => 'Rasenfläche Details'])]
    public function render(): View
    {
        return view('livewire.lawn.lawn-show');
    }
}
