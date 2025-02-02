<?php

declare(strict_types=1);

namespace App\Livewire\Components\Navigation;

use Livewire\Component;

final class NavMenuDropdown extends Component
{
    public bool $isOpen = false;

    public function toggleDropdown(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function render()
    {
        return view('livewire.components.navigation.nav-menu-dropdown');
    }
}
