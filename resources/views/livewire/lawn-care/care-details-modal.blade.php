<div
    x-data="{
        show: @entangle('isOpen').live,
        isEditing: @entangle('isEditing').live
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
                                    {{ $careType?->label() }} Details
                                </h2>
                                <div class="flex items-center space-x-3">
                                    <button
                                        type="button"
                                        wire:click="toggleEdit"
                                        class="rounded-md bg-primary-700 text-white hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-white"
                                    >
                                        <span class="sr-only">
                                            {{ $isEditing ? 'Speichern' : 'Bearbeiten' }}
                                        </span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                             stroke="currentColor">
                                            @if($isEditing)
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M4.5 12.75l6 6 9-13.5"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                            @endif
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="$dispatch('confirm-delete-care', { care: {{ $care?->id }} })"
                                        class="rounded-md bg-primary-700 text-white hover:text-red-200 focus:outline-none focus:ring-2 focus:ring-white"
                                    >
                                        <span class="sr-only">Löschen</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                             stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="close"
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

                        <div class="flex-1 px-4 py-6 sm:px-6">
                            @if($isEditing)
                                <!-- Edit Form -->
                                <form wire:submit="save" class="space-y-6">
                                    @error('form')
                                    <div
                                        class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                    <!-- Common Fields -->
                                    <div class="space-y-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Durchgeführt
                                                am</label>
                                            <input
                                                type="datetime-local"
                                                wire:model="performed_at"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                            >
                                            @error('performed_at') <span
                                                class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Geplant für</label>
                                            <input
                                                type="datetime-local"
                                                wire:model="scheduled_for"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                            >
                                            @error('scheduled_for') <span
                                                class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Notizen</label>
                                            <textarea
                                                wire:model="notes"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                                rows="3"
                                            ></textarea>
                                            @error('notes') <span
                                                class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Type-Specific Fields -->
                                    @switch($careType)
                                        @case(\App\Enums\LawnCare\LawnCareType::MOW)
                                            @include('livewire.lawn-care.partials.mowing-fields')
                                            @break

                                        @case(\App\Enums\LawnCare\LawnCareType::FERTILIZE)
                                            @include('livewire.lawn-care.partials.fertilizing-fields')
                                            @break

                                        @case(\App\Enums\LawnCare\LawnCareType::WATER)
                                            @include('livewire.lawn-care.partials.watering-fields')
                                            @break
                                    @endswitch
                                </form>
                            @else
                                <!-- Read-only View -->
                                <div class="space-y-6">
                                    <!-- Common Information -->
                                    <div class="space-y-4">
                                        @if($performed_at)
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-500">Durchgeführt am</h4>
                                                <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($performed_at)->format('d.m.Y H:i') }}</p>
                                            </div>
                                        @endif

                                        @if($scheduled_for)
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-500">Geplant für</h4>
                                                <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($scheduled_for)->format('d.m.Y H:i') }}</p>
                                            </div>
                                        @endif

                                        @if($notes)
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-500">Notizen</h4>
                                                <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $notes }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Type-Specific Information -->
                                    <div class="border-t border-gray-200 pt-6">
                                        @switch($careType)
                                            @case(\App\Enums\LawnCare\LawnCareType::MOW)
                                                @include('livewire.lawn-care.partials.mowing-details')
                                                @break

                                            @case(\App\Enums\LawnCare\LawnCareType::FERTILIZE)
                                                @include('livewire.lawn-care.partials.fertilizing-details')
                                                @break

                                            @case(\App\Enums\LawnCare\LawnCareType::WATER)
                                                @include('livewire.lawn-care.partials.watering-details')
                                                @break
                                        @endswitch
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
