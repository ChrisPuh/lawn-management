<div
    x-data="{ show: @entangle('isOpen').live }"
    x-show="show"
    x-cloak
    class="relative z-50"
>
    <!-- Background backdrop -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div class="pointer-events-auto w-screen max-w-md">
                    <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
                        <!-- Header -->
                        <div class="bg-primary-700 px-4 py-6 sm:px-6">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-medium text-white">
                                    Neue Pflegemaßnahme
                                </h2>
                                <button
                                    type="button"
                                    wire:click="close"
                                    class="rounded-md bg-primary-700 text-white hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-white"
                                >
                                    <span class="sr-only">Schließen</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Form content -->
                        <form wire:submit="save" class="flex-1 px-4 py-6 sm:px-6">
                            @error('form')
                            <div class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                                {{ $message }}
                            </div>
                            @enderror

                            <!-- Care Type Selection -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pflegemaßnahme</label>
                                <div class="grid grid-cols-3 gap-3">
                                    @foreach($careTypes as $type)
                                        <button
                                            type="button"
                                            wire:click="updateType('{{ $type->value }}')"
                                            class="relative px-3 py-2 text-sm font-medium rounded-md border {{ $selectedType === $type ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}"
                                        >
                                            {{ $type->label() }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Common Fields -->
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Durchgeführt am</label>
                                    <input
                                        type="datetime-local"
                                        wire:model="performed_at"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                    >
                                    @error('performed_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Geplant für</label>
                                    <input
                                        type="datetime-local"
                                        wire:model="scheduled_for"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                    >
                                    @error('scheduled_for') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Notizen</label>
                                    <textarea
                                        wire:model="notes"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                        rows="3"
                                    ></textarea>
                                    @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Type-Specific Fields -->
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                @switch($selectedType)
                                    @case(App\Enums\LawnCare\LawnCareType::MOW)
                                        @include('livewire.lawn-care.partials.mowing-fields')
                                        @break

                                    @case(App\Enums\LawnCare\LawnCareType::FERTILIZE)
                                        @include('livewire.lawn-care.partials.fertilizing-fields')
                                        @break

                                    @case(App\Enums\LawnCare\LawnCareType::WATER)
                                        @include('livewire.lawn-care.partials.watering-fields')
                                        @break
                                @endswitch
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-6 flex justify-end">
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                >
                                    Speichern
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
