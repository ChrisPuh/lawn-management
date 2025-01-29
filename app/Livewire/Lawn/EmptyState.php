<?php

declare(strict_types=1);

namespace App\Livewire\Lawn;

use Livewire\Component;

final class EmptyState extends Component
{
    public function createLawn(): void
    {
        $this->dispatch('createLawn');
    }

    public function render(): string
    {
        return
            <<<'blade'
                <div class="col-span-full flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Keine Rasenflächen</h3>
                    <p class="mt-1 text-sm text-gray-500">Erstellen Sie Ihre erste Rasenfläche um zu beginnen.</p>
                    <div class="mt-6">
                          <a href="{{route('lawn.create')}}"
                        class="inline-flex items-center rounded-md bg-primary-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-600">
                            <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                            </svg>
                            Rasenfläche anlegen
                        </a>
                    </div>
                </div>
                blade;
    }
}
