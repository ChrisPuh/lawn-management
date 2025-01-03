<?php

declare(strict_types=1);

namespace App\Livewire\Lawn;

use App\Models\Lawn;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

final class LawnIndex extends Component
{
    #[Layout('components.layouts.authenticated.index', ['title' => 'Rasenflächen'])]
    public function render(): View
    {
        $lawns = Lawn::forUser()
            ->with('mowingRecords')
            ->get();

        $lastMowedDate = $lawns->map(fn ($lawn) => $lawn->getLastMowingDate('Y-m-d'))
            ->filter()
            ->sort()
            ->last();

        return view('livewire.lawn.lawn-index', [
            'lawns' => $lawns,
            'lastMowedDate' => $lastMowedDate ? date('d.m.Y', strtotime($lastMowedDate)) : null,
        ]);
    }

    /**
     * Redirect to the create lawn page
     */
    #[On('createLawn')]
    public function createLawn(): void
    {
        $this->redirect(route('lawn.create'), navigate: true);
    }

    /**
     * Redirect to the show lawn page
     */
    #[On('showLawn')]
    public function showLawn(int $id): void
    {
        $this->redirect(route('lawn.show', $id), navigate: true);
    }

    /**
     * Redirect to the edit lawn page
     */
    #[On('editLawn')]
    public function editLawn(int $id): void
    {
        $this->redirect(route('lawn.edit', $id), navigate: true);
    }
}
