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

    public string $onConfirm;

    public function mount(string $title, string $message, string $onConfirm): void
    {
        $this->title = $title;
        $this->message = $message;
        $this->onConfirm = $onConfirm;
    }

    public function toggle(): void
    {
        $this->show = ! $this->show;
    }

    public function confirm(): void
    {
        $this->dispatch($this->onConfirm);
        $this->show = false;
    }

    public function render(): View
    {
        return view('livewire.components.delete-modal');
    }
}
