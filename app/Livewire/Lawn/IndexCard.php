<?php

declare(strict_types=1);

namespace App\Livewire\Lawn;

use App\Models\Lawn;
use Livewire\Attributes\Layout;
use Livewire\Component;

final class IndexCard extends Component
{
    public Lawn $lawn;

    public function showLawn()
    {
        $this->dispatch('showLawn', $this->lawn->id);
    }

    public function editLawn()
    {
        $this->dispatch('editLawn', $this->lawn->id);
    }

    #[Layout('components.layouts.authenticated.index', ['title' => ''])]
    public function render()
    {
        return view('livewire.lawn.index-card');
    }
}
