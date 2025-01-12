<div class="flex w-full flex-col items-center justify-center space-y-4">
    <!-- Image Container -->
    <div class="relative aspect-video w-full rounded-lg bg-gray-100">
        @if ($showConfirmation && $image)
            <!-- Image Preview -->
            <img src="{{ $image->temporaryUrl() }}" alt="Bildvorschau" class="h-full w-full rounded-lg object-cover">
            <div class="absolute inset-x-0 bottom-0 flex items-center justify-center gap-x-3 bg-black/50 p-4">
                <button wire:click="cancelUpload" type="button"
                    class="inline-flex items-center gap-x-2 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Abbrechen
                </button>
                <button wire:click="save" type="button"
                    class="inline-flex items-center gap-x-2 rounded-md bg-primary-500 px-3 py-2 text-sm font-semibold text-white hover:bg-primary-600">
                    Speichern
                </button>
            </div>
        @elseif($latestImage)
            <!-- Current Image -->
            <img src="{{ Storage::url($latestImage->image_path) }}" alt="Aktuelles Rasenbild"
                class="h-full w-full rounded-lg object-cover">
            <button wire:click="delete({{ $latestImage->id }})"
                class="absolute right-2 top-2 rounded-full bg-white/80 p-1.5 text-gray-700 transition hover:bg-red-100 hover:text-red-600"
                title="Bild löschen">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </button>
        @else
            <!-- Empty State Placeholder -->
            <div
                class="absolute inset-0 flex flex-col items-center justify-center space-y-3 bg-gradient-to-br from-primary-100 to-primary-50">
                <div class="rounded-lg bg-white/80 p-4 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-10 w-10 text-primary-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                    </svg>
                </div>
                <span class="text-center">
                    <span class="block font-medium text-gray-900">Noch kein Bild vorhanden</span>
                    <span class="mt-1 block text-sm text-gray-500">Klicken Sie unten auf "Bild auswählen"</span>
                </span>
            </div>
        @endif

        @if ($image && !$showConfirmation)
            <!-- Loading State -->
            <div wire:loading.delay wire:target="image"
                class="absolute inset-0 flex items-center justify-center rounded-lg bg-gray-900 bg-opacity-50">
                <div class="flex items-center space-x-2 text-white">
                    <svg class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span>Wird verarbeitet...</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Upload Input & Button -->
    <div class="flex justify-center">
        <input type="file" wire:model="image" class="hidden" id="lawn-image-upload" accept="image/*">
        <label for="lawn-image-upload"
            class="inline-flex cursor-pointer items-center gap-x-2 rounded-md bg-primary-500 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-600"
            wire:loading.class="opacity-50 cursor-not-allowed" wire:loading.attr="disabled">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
            </svg>
            {{ $latestImage ? 'Bild ändern' : 'Bild auswählen' }}
        </label>
    </div>

    @error('image')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
