<?php

declare(strict_types=1);

namespace App\Livewire\Lawn;

use App\Models\Lawn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

final class LawnIndex extends Component
{
    #[Layout('components.layouts.authenticated.index', ['title' => 'Rasenflächen'])]
    public function render(): View
    {
        /** @var Collection<int, Lawn> $lawns */
        $lawns = Lawn::query()
            ->forUser()
            ->with(['mowingRecords', 'fertilizingRecords', 'scarifyingRecords', 'aeratingRecords'])
            ->get();

        $careDates = $lawns->mapWithKeys(fn (Lawn $lawn) => [
            $lawn->id => $this->getLatestCare($lawn),
        ]);

        return view('livewire.lawn.lawn-index', [
            'lawns' => $lawns,
            'careDates' => $careDates,
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

    /**
     * @return array{
     *     type: string,
     *     date: string
     * }|null
     */
    private function getLatestCare(Lawn $lawn): ?array
    {
        $careDates = [
            'Mähen' => $lawn->getLastMowingDate('Y-m-d'),
            'Düngen' => $lawn->getLastFertilizingDate('Y-m-d'),
            'Vertikutieren' => $lawn->getLastScarifyingDate('Y-m-d'),
            'Aerifizieren' => $lawn->getLastAeratingDate('Y-m-d'),
        ];

        $latestCare = collect($careDates)
            ->filter()
            ->map(fn ($date, $type) => [
                'type' => $type,
                'date' => $date,
            ])
            ->sortByDesc('date')
            ->first();

        if (! $latestCare) {
            return null;
        }

        return [
            'type' => $latestCare['type'],
            'date' => date('d.m.Y', strtotime($latestCare['date'])),
        ];
    }
}
