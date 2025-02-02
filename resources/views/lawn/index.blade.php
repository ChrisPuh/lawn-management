<x-layouts.authenticated.index :title="$title">
    <x-slot name="actions">
        create
    </x-slot>
    <!-- resources/views/livewire/lawn/lawn-index.blade.php -->
    <div class="space-y-8">
        <livewire:lawn.overview-stats-card :total-lawns="$lawns->count()" :last-care-info="$lastCareInfo"/>

        <!-- Lawn List -->
        <div>
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Meine Rasenflächen</h3>
                <a href="{{route('lawn.create')}}"
                   class="inline-flex items-center rounded-md bg-primary-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-600">
                    <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                         fill="currentColor">
                        <path
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/>
                    </svg>
                    Neue Rasenfläche</a>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($lawns as $lawn)
                    <livewire:lawn.index-card :lawn="$lawn" :care-date="$careDates[$lawn->id]" :wire:key="$lawn->id"/>
                @empty
                    <livewire:lawn.empty-state/>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.authenticated.index>
