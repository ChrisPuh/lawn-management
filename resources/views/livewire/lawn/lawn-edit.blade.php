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
        <div class="border-b border-primary-200 px-6 py-4">
            <h2 class="text-xl font-semibold text-gray-900">Rasenfläche bearbeiten</h2>
        </div>
        <div class="p-6">
            <form wire:submit="save">
                {{ $this->form }}

                <div class="mt-6 flex justify-end gap-x-3">
                    <a href="{{ route('lawn.show', $lawn) }}" wire:navigate
                        class="rounded-lg px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Abbrechen
                    </a>
                    <button type="submit"
                        class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-600">
                        Änderungen speichern
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
