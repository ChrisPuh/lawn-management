<div>
    <x-slot name="actions">
        <a href="{{ route('lawn.index') }}" wire:navigate
            class="flex items-center gap-x-1 text-sm font-medium text-gray-600 hover:text-gray-900">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z"
                    clip-rule="evenodd" />
            </svg>
            Zurück zur Übersicht
        </a>
    </x-slot>

    <div class="space-y-8">
        <div class="overflow-hidden rounded-lg border border-primary-200 bg-white shadow-sm">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-primary-200 px-6 py-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $lawn->name }}</h2>
                    <p class="text-sm text-gray-500">Erstellt am {{ $lawn->created_at->format('d.m.Y') }}</p>
                </div>
                <div>
                    <span
                        class="inline-flex items-center rounded-full bg-nature-grass-healthy px-2.5 py-0.5 text-xs font-medium text-white">
                        {{ $lawn->type?->label() ?? 'Nicht spezifiziert' }}
                    </span>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid gap-6 p-6 md:grid-cols-3">
                <!-- Left Column - Details -->
                <div class="space-y-6">
                    <div>
                        <h3 class="font-medium text-gray-900">Details</h3>
                        <dl class="mt-2 grid grid-cols-2 gap-4">
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
                                <dd class="text-sm text-gray-900">{{ $lastMowingDate ?? 'Keine Pflege' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Center Column - Current Image -->
                <div class="flex flex-col items-center justify-center space-y-4">
                    <div class="relative h-64 w-full rounded-lg bg-gray-100">
                        <div class="absolute inset-0 flex flex-col items-center justify-center space-y-2">
                            <svg class="h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-sm text-gray-500">Bildupload in Kürze verfügbar</span>
                        </div>
                    </div>
                    <button type="button" disabled
                        class="inline-flex cursor-not-allowed items-center gap-x-2 rounded-md bg-gray-400 px-4 py-2 text-sm font-semibold text-white">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M1 8a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 018.07 3h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0016.07 6H17a2 2 0 012 2v7a2 2 0 01-2 2H3a2 2 0 01-2-2V8zm13.5 3a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM10 14a3 3 0 100-6 3 3 0 000 6z"
                                clip-rule="evenodd" />
                        </svg>
                        Bild hochladen
                    </button>
                </div>

                <!-- Right Column - History -->
                <div>
                    <h3 class="font-medium text-gray-900">Pflegehistorie</h3>
                    <div class="mt-2 space-y-3">
                        <div class="rounded-md bg-gray-50 px-4 py-3">
                            <div class="text-sm font-medium text-gray-900">Letzte Mahd</div>
                            <div class="text-sm text-gray-500">{{ $lastMowingDate ?? 'Noch nie' }}</div>
                        </div>
                        <div class="rounded-md bg-gray-50 px-4 py-3">
                            <div class="text-sm font-medium text-gray-900">Letzte Düngung</div>
                            <div class="text-sm text-gray-500">{{ $lastFertilizingDate ?? 'Noch nie' }}</div>
                        </div>
                        <div class="rounded-md bg-gray-50 px-4 py-3">
                            <div class="text-sm font-medium text-gray-900">Letztes Vertikutieren</div>
                            <div class="text-sm text-gray-500">{{ $lastScarifyingDate ?? 'Noch nie' }}</div>
                        </div>
                        <div class="rounded-md bg-gray-50 px-4 py-3">
                            <div class="text-sm font-medium text-gray-900">Letzte Aerifizierung</div>
                            <div class="text-sm text-gray-500">{{ $lastAeratingDate ?? 'Noch nie' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between border-t border-primary-200 px-6 py-4">
                <div>
                    <livewire:components.delete-modal :title="'Rasenfläche löschen'" :message="'Möchten Sie die Rasenfläche \'' .
                        $lawn->name .
                        '\' wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.'" :on-confirm="'delete-confirmed'" />
                </div>
                <div class="flex gap-x-3">
                    <a href="{{ route('lawn.edit', $lawn) }}" wire:navigate
                        class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-600">
                        Bearbeiten
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
