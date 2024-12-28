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

            <div class="grid gap-6 p-6 sm:grid-cols-2">
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

                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="font-medium text-gray-900">Mähverlauf</h3>
                        <button wire:click="openModal"
                            class="text-sm font-medium text-primary-600 hover:text-primary-500">
                            Neuen Eintrag erstellen
                        </button>

                        <!-- Modal -->
                        <div x-show="$wire.isModalOpen" x-cloak class="fixed inset-0 z-10 overflow-y-auto"
                            aria-labelledby="modal-title" x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0">
                            <div
                                class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                                <!-- Background overlay -->
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                                    aria-hidden="true">
                                </div>

                                <!-- Modal panel -->
                                <div
                                    class="inline-block transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6 sm:align-middle">
                                    <div class="absolute right-0 top-0 pr-4 pt-4">
                                        <button wire:click="closeModal" type="button"
                                            class="rounded-md bg-white text-gray-400 hover:text-gray-500">
                                            <span class="sr-only">Schließen</span>
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="sm:flex sm:items-start">
                                        <div class="mt-3 w-full text-center sm:ml-4 sm:mt-0 sm:text-left">
                                            <h3 class="text-base font-semibold leading-6 text-gray-900"
                                                id="modal-title">
                                                Neuer Mäheintrag
                                            </h3>
                                            <div class="mt-4">
                                                <form wire:submit="create">
                                                    {{ $this->form }}

                                                    <div class="mt-6 flex justify-end gap-x-3">
                                                        <button type="button" wire:click="closeModal"
                                                            class="rounded-lg px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                                            Abbrechen
                                                        </button>
                                                        <button type="submit"
                                                            class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-600">
                                                            Eintrag erstellen
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 flow-root">
                        <div class="-my-2 overflow-x-auto">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden rounded-lg border border-gray-200">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    Datum
                                                </th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                    Schnitthöhe
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @forelse($mowingRecords as $record)
                                                <tr>
                                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                                        {{ $record->mowed_on->format('d.m.Y') }}
                                                    </td>
                                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                        {{ $record->cutting_height ?? 'Nicht angegeben' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2"
                                                        class="px-6 py-4 text-center text-sm text-gray-500">
                                                        Noch keine Mäheinträge vorhanden
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-primary-200 px-6 py-4">
                <div class="flex justify-between">
                    <button wire:click="$toggle('showDeleteModal')"
                        class="rounded-lg px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">
                        Rasenfläche löschen
                    </button>
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
        <!-- Delete Modal -->
        <div x-show="$wire.showDeleteModal" x-cloak class="fixed inset-0 z-10 overflow-y-auto"
            aria-labelledby="modal-title">
            <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <!-- Modal panel -->
                <div
                    class="inline-block transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6 sm:align-middle">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">
                                Rasenfläche löschen
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Möchten Sie die Rasenfläche "{{ $lawn->name }}" wirklich löschen? Diese Aktion
                                    kann nicht rückgängig gemacht werden.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="confirmDelete"
                            class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                            Löschen
                        </button>
                        <button type="button" wire:click="$toggle('showDeleteModal')"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                            Abbrechen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

