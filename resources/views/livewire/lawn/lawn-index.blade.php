<x-slot name="actions">
    create
</x-slot>
<!-- resources/views/livewire/lawn/lawn-index.blade.php -->
<div class="space-y-8">
    <!-- Overview Stats Card -->
    <div class="overflow-hidden rounded-lg border border-primary-200 bg-white shadow-sm">
        <div class="border-b border-primary-200 px-6 py-4">
            <h2 class="text-xl font-semibold text-gray-900">Rasenflächen Übersicht</h2>
        </div>
        <div class="grid gap-6 px-6 py-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <dt class="text-sm font-medium text-gray-500">Gesamtanzahl Rasenflächen</dt>
                <dd class="text-sm text-gray-900">{{ $lawns->count() }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Letzte Pflege</dt>
                <dd class="text-sm text-gray-900">
                    {{ $lastMowedDate ?? 'Keine Pflege eingetragen' }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Nächste geplante Pflege</dt>
                <dd class="text-sm text-gray-900">Wird implementiert</dd>
            </div>
        </div>
    </div>

    <!-- Lawn List -->
    <div>
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Meine Rasenflächen</h3>
            <button wire:click="createLawn"
                class="inline-flex items-center rounded-md bg-primary-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-600">
                <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                </svg>
                Neue Rasenfläche
            </button>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($lawns as $lawn)
                <div class="flex flex-col rounded-lg border border-primary-200 bg-white shadow-sm">
                    <div class="flex-1 p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $lawn->name }}</h3>
                            <span
                                class="inline-flex items-center rounded-full bg-nature-grass-healthy px-2.5 py-0.5 text-xs font-medium text-white">
                                {{ $lawn->type?->label() ?? 'Nicht spezifiziert' }}
                            </span>
                        </div>
                        <dl class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Standort</dt>
                                <dd class="text-sm text-gray-900">{{ $lawn->location ?? 'Nicht angegeben' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Größe</dt>
                                <dd class="text-sm text-gray-900">{{ $lawn->size ?? 'Nicht angegeben' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Grassorte</dt>
                                <dd class="text-sm text-gray-900">{{ $lawn->grass_seed?->label() ?? 'Nicht angegeben' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Letzte Pflege</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ $lawn->getLastMowingDate() ?? 'Keine Pflege' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                    <div class="flex divide-x divide-primary-200 border-t border-primary-200">
                        <button wire:click="showLawn({{ $lawn->id }})"
                            class="flex flex-1 items-center justify-center py-3 text-sm font-medium text-primary-600 hover:bg-primary-50">
                            Details
                        </button>
                        <button wire:click="editLawn({{ $lawn->id }})"
                            class="flex flex-1 items-center justify-center py-3 text-sm font-medium text-primary-600 hover:bg-primary-50">
                            Bearbeiten
                        </button>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Keine Rasenflächen</h3>
                    <p class="mt-1 text-sm text-gray-500">Erstellen Sie Ihre erste Rasenfläche um zu beginnen.</p>
                    <div class="mt-6">
                        <button wire:click="createLawn"
                            class="inline-flex items-center rounded-md bg-primary-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-600">
                            <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                            </svg>
                            Rasenfläche anlegen
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
