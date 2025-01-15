<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Livewire\Attributes\On;
use Livewire\Component;

final class SuccessMessage extends Component
{
    public string $message = 'Erfolgreich aktualisiert';

    public int $duration = 3000;

    public bool $show = true;

    public function mount(
        string $message = 'Erfolgreich aktualisiert',
        int $duration = 3000
    ): void {
        $this->message = $message;
        $this->duration = $duration;
    }

    #[On('show-success-message')]
    public function showMessage(string $message, int $duration = 3000): void
    {
        $this->message = $message;
        $this->duration = $duration;
        $this->show = true;
    }

    public function hide(): void
    {
        $this->show = false;
        $this->dispatch('hide-success');
    }

    public function render(): string
    {
        return
            <<<'Blade'
        <div>
            @if ($show && $message)
                <div wire:transition.opacity.duration.1000ms
                    x-data="{
                        init() {
                            setTimeout(() => this.$wire.hide(), {{ $duration }})
                        }
                    }"
                    class="absolute inset-x-0 top-0 mx-auto flex w-auto max-w-sm items-center       justify-center gap-x-2 bg-success-500/90 px-4 py-2 text-sm font-medium      text-white shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24     24"     stroke-width="1.            5"
                            stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118     0z" />
                        </svg>
                    {{ $message }}
                </div>
            @endif
        </div>
        Blade;
    }
}
