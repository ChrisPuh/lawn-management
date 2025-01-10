<div class="flex w-full flex-col items-center justify-center space-y-4">
    <div class="relative aspect-video w-full rounded-lg bg-gray-100">
        @if ($latestImage)
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
            <div class="absolute inset-0 flex flex-col items-center justify-center space-y-3">
                <div class="rounded-full bg-gray-100 p-3">
                    <svg class="h-8 w-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
                <span class="text-sm text-gray-500">Noch kein Bild vorhanden</span>
            </div>
        @endif

        @if ($image)
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
                    <span>Wird hochgeladen...</span>
                </div>
            </div>
        @endif
    </div>

    <div>
        <input type="file" wire:model="image" class="hidden" id="lawn-image-upload" accept="image/*">
        <label for="lawn-image-upload"
            class="inline-flex cursor-pointer items-center gap-x-2 rounded-md bg-primary-500 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-600"
            wire:loading.class="opacity-50 cursor-not-allowed" wire:loading.attr="disabled">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
            </svg>
            Bild hochladen
        </label>
    </div>

    @error('image')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
