<div
    x-data="{
        show: @entangle('isOpen'),
    }"
    x-show="show"
    x-cloak
    class="relative z-50"
>
    <!-- Background backdrop -->
    <div
        x-show="show"
        x-transition:enter="ease-in-out duration-500"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in-out duration-500"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
    ></div>

    <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div
                x-show="show"
                x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10"
            >
                <div class="pointer-events-auto w-screen max-w-md">
                    <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
                        <!-- Header -->
                        <div class="bg-primary-700 px-4 py-6 sm:px-6">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-medium text-white">
                                    {{ $care?->type?->label() }} Details
                                </h2>
                                <div class="flex items-center space-x-3">
                                    <button
                                        type="button"
                                        wire:click="toggleEdit"
                                        class="rounded-md bg-primary-700 text-white hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-white"
                                    >
                                        <span class="sr-only">{{ $isEditing ? 'Speichern' : 'Bearbeiten' }}</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                             stroke="currentColor">
                                            @if($isEditing)
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M9 3.75H6.912a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859M12 3v8.25m0 0l-3-3m3 3l3-3"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                            @endif
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="$set('isOpen', false)"
                                        class="rounded-md bg-primary-700 text-white hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-white"
                                    >
                                        <span class="sr-only">Schließen</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                             stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Form content -->
                        <div class="relative flex-1 px-4 sm:px-6">
                            <div class="absolute inset-0 top-4 px-4 sm:px-6">
                                <div class="h-full" aria-hidden="true">
                                    <form wire:submit="save">
                                        {{ $this->form }}
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
