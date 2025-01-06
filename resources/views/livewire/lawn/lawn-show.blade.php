<x-slot name="actions">
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
</x-slot>
<div>
    <!-- resources/views/livewire/lawn/lawn-show.blade.php -->
    <div class="space-y-8">
        <!-- Rasenfläche Info Card -->
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

            <!-- Body -->
            <div class="grid gap-6 p-6 sm:grid-cols-2">
                <!-- left -->
                <div>
                    <h3 class="font-medium text-gray-900">Details</h3>
                    <dl class="mt-2 grid grid-cols-1 gap-4 sm:grid-cols-2">
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
                <!-- right -->
                <div>
                    <div class="flex items-center justify-between">



                    </div>
                    <div class="mt-2 flow-root">

                        
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-primary-200 px-6 py-4">
                <div class="flex justify-between">
                    <div>
                        <livewire:components.delete-modal :title="'Rasenfläche löschen'" :message="'Möchten Sie die Rasenfläche \'' .
                            $lawn->name .
                            '\' wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.'" :on-confirm="'delete-confirmed'">

                        </livewire:components.delete-modal>
                    </div>
                    <div class="flex gap-x-3">
                        <a href="{{ route('lawn.index') }}" wire:navigate
                            class="rounded-lg px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            Zurück zur Übersicht
                        </a>
                        <a href="{{ route('lawn.edit', $lawn) }}" wire:navigate
                            class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-600">
                            Bearbeiten
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
