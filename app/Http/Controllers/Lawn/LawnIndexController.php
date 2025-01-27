<?php

namespace App\Http\Controllers\Lawn;

use App\Enums\LawnCare\LawnCareType;
use App\Models\Lawn;
use App\Models\LawnCare;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;

class LawnIndexController
{

    public function __invoke(): View
    {

        /** @var Collection<int, Lawn> $lawns */
        $lawns = Lawn::query()
            ->forUser()
            ->with(['lawnCares' => function ($query): void {
                $query->whereNotNull('performed_at')
                    ->orderByDesc('performed_at');
            }])
            ->get();

        $careDates = $lawns->mapWithKeys(fn (Lawn $lawn) => [
            $lawn->id => $lawn->getLatestCare(),
        ]);

        $lastCareInfo = $this->getLastCareAcrossAllLawns($lawns);

        return view('lawn.index', [
            'title' => 'Rasenflächen',
            'lawns' => $lawns,
            'careDates' => $careDates,
            'lastCareInfo' => $lastCareInfo,
        ]);
    }

    /**
     * Get the latest care information across all lawns
     *
     * @param  Collection<int, Lawn>  $lawns
     * @return array{lawn: string, type: string, date: string}|null
     */
    private function getLastCareAcrossAllLawns(Collection $lawns): ?array
    {
        /** @var LawnCare|null $latestCare */
        $latestCare = LawnCare::query()
            ->whereIn('lawn_id', $lawns->pluck('id'))
            ->whereNotNull('performed_at')
            ->latest('performed_at')
            ->with('lawn:id,name')
            ->first();

        if (! $latestCare || ! $latestCare->performed_at) {
            return null;
        }

        return [
            'lawn' => $latestCare->lawn->name,
            'type' => $this->formatCareType($latestCare->type),
            'date' => $latestCare->performed_at->format('d.m.Y'),
        ];
    }

    private function formatCareType(LawnCareType $type): string
    {
        return $type->pastTense();

    }

}
