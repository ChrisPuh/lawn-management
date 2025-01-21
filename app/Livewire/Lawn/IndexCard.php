<?php

declare(strict_types=1);

namespace App\Livewire\Lawn;

use App\Models\Lawn;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class IndexCard extends Component
{
    public Lawn $lawn;

    /** @var array{type: string, date: string}|null */
    public ?array $careDate;

    public function mount(Lawn $lawn, ?array $careDate): void
    {
        $this->lawn = $lawn;
        $this->careDate = $careDate;
    }

    public function showLawn(): void
    {
        $this->dispatch('showLawn', $this->lawn->id);
    }

    public function editLawn(): void
    {
        $this->dispatch('editLawn', $this->lawn->id);
    }

    public function render(): View
    {
        return view('livewire.lawn.index-card');
    }
}
