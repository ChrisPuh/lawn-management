<?php

declare(strict_types=1);

namespace App\Livewire\Lawn;

use App\Models\Lawn;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

final class LawnShow extends Component
{
    public Lawn $lawn;

    /**
     * Mounts the component
     */
    public function mount(): void
    {
        $this->authorize('view', $this->lawn);
    }

    /**
     * Deletes the lawn and redirects to the lawn index
     */
    #[On('deleteConfirmed')]
    public function deleteLawn(): void
    {
        $this->authorize('delete', $this->lawn);

        $this->lawn->delete();
        $this->redirect(route('lawn.index'), navigate: true);
    }

    #[Layout('components.layouts.authenticated.index', ['title' => 'Rasenfläche Details'])]
    public function render(): View
    {
        return view('livewire.lawn.lawn-show', []);
    }
}
