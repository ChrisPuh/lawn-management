<?php

declare(strict_types=1);

namespace App\Livewire\Components\Navigation;

use Livewire\Component;

final class UserMenuDropdown extends Component
{
    public bool $isOpen = false;

    public function toggleDropdown(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function render()
    {
        return view('livewire.components.navigation.user-menu-dropdown');
    }
}
