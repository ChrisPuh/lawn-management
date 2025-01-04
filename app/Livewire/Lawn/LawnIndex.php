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

        $lastCareInfo = $this->getLastCareAcrossAllLawns($lawns);

        return view('livewire.lawn.lawn-index', [
            'lawns' => $lawns,
            'careDates' => $careDates,
            'lastCareInfo' => $lastCareInfo,
        ]);
    }

    #[On('createLawn')]
    public function createLawn(): void
    {
        $this->redirect(route('lawn.create'), navigate: true);
    }

    #[On('showLawn')]
    public function showLawn(int $id): void
    {
        $this->redirect(route('lawn.show', $id), navigate: true);
    }

    #[On('editLawn')]
    public function editLawn(int $id): void
    {
        $this->redirect(route('lawn.edit', $id), navigate: true);
    }

    /**
     * Get the latest care information across all lawns
     *
     * @param  Collection<int, Lawn>  $lawns
     * @return array{lawn: string, type: string, date: string}|null
     */
    private function getLastCareAcrossAllLawns(Collection $lawns): ?array
    {
        $allCares = $lawns->map(function (Lawn $lawn) {
            $latestCare = $this->getLatestCare($lawn);
            if (! $latestCare) {
                return null;
            }

            return [
                'lawn' => $lawn->name,
                'type' => $latestCare['type'],
                'date' => $latestCare['date'],
                'timestamp' => strtotime($latestCare['date']),
            ];
        })
            ->filter();

        if ($allCares->isEmpty()) {
            return null;
        }

        $latest = $allCares->sortByDesc('timestamp')->first();

        return [
            'lawn' => $latest['lawn'],
            'type' => $latest['type'],
            'date' => $latest['date'],
        ];
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
            'gemäht' => $lawn->getLastMowingDate('Y-m-d'),
            'gedüngt' => $lawn->getLastFertilizingDate('Y-m-d'),
            'vertikutiert' => $lawn->getLastScarifyingDate('Y-m-d'),
            'aerifiziert' => $lawn->getLastAeratingDate('Y-m-d'),
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
