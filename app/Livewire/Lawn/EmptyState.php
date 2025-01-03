<?php

declare(strict_types=1);

namespace App\Livewire\Lawn;

use Livewire\Attributes\Layout;
use Livewire\Component;

final class EmptyState extends Component
{
    public function createLawn(): void
    {
        $this->dispatch('createLawn');
    }

    #[Layout('components.layouts.authenticated.index', ['title' => ''])]
    public function render()
    {
        return view('livewire.lawn.empty-state');
    }
}
