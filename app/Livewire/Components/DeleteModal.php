<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Illuminate\Contracts\View\View;
use Livewire\Component;

final class DeleteModal extends Component
{
    public bool $show = false;

    public string $title;

    public string $message;

    public function render(): View
    {
        return view('livewire.components.delete-modal');
    }
}
