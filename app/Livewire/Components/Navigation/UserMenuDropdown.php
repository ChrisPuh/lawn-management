<?php

namespace App\Livewire\Components\Navigation;

use Livewire\Component;

class UserMenuDropdown extends Component
{
    public bool $isOpen = false;

    public function toggleDropdown()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function render()
    {
        return view('livewire.components.navigation.user-menu-dropdown');
    }
}
