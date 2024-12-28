<?php

declare(strict_types=1);

namespace App\Livewire\Lawn;

use App\Models\Lawn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

/** @property-read Collection<int, Lawn> $lawns */
final class LawnIndex extends Component
{
    #[Layout('components.layouts.authenticated.index', ['title' => 'Rasenflächen'])]
    public function render(): View
    {
        /** @todo extract to repository */
        $lawns = Lawn::query()
            ->with('mowingRecords')
            ->get();

        /** @todo extract to repository */
        $lastMowedDate = $lawns->map(function ($lawn) {
            return $lawn->getLastMowingDate('Y-m-d');
        })
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
    public function createLawn(): void
    {
        $this->redirect(route('lawn.create'), navigate: true);
    }

    /**
     * Redirect to the show lawn page
     */
    public function showLawn(int $id): void
    {
        $this->redirect(route('lawn.show', $id), navigate: true);
    }

    /**
     * Redirect to the edit lawn page
     */
    public function editLawn(int $id): void
    {
        $this->redirect(route('lawn.edit', $id), navigate: true);
    }
}
