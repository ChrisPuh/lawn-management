<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900">Pflegeeinträge</h3>
        <div class="flex items-center gap-x-4">
            <!-- Type Filter -->
            <div>
                <select wire:model.live="selectedType"
                        class="block w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Alle Arten</option>
                    @foreach($this->types as $type)
                        <option value="{{ $type['value'] }}">
                            {{ $type['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Add New Entry Button -->
            <button type="button"
                    wire:click="$dispatch('show-create-care', { lawnId: {{ $lawn->id }} })"
                    class="inline-flex items-center gap-x-2 rounded-lg bg-primary-500 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Neuer Eintrag
            </button>
        </div>
    </div>

    <!-- Care Entries List -->
    <div class="divide-y divide-gray-200 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
        @forelse($this->lawnCares as $care)
            <div class="flex items-center gap-x-4 p-4 hover:bg-gray-50">
                <!-- Icon -->
                <div
                    class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                    <x-lawn-care-icon :path="$care->type->iconPath()"/>
                </div>

                <!-- Content -->
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between">
                        <p class="truncate text-sm font-medium text-gray-900">
                            {{ $care->type->label() }}
                        </p>
                        <div class="ml-4 flex flex-shrink-0">
                            <span
                                class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $care->isCompleted() ? 'bg-success-100 text-success-700' : 'bg-warning-100 text-warning-700' }}">
                                {{ $care->getStatus() }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-1">
                        <p class="text-sm text-gray-500">
                            Durchgeführt von {{ $care->createdBy->name }}
                            am {{ $care->performed_at?->format('d.m.Y H:i') ?? 'Nicht angegeben' }}
                        </p>
                    </div>
                    @if($care->notes)
                        <p class="mt-2 text-sm text-gray-500">{{ $care->notes }}</p>
                    @endif
                </div>
                <!-- Actions -->
                <div class="flex flex-shrink-0 items-center gap-x-2">
                    <button type="button"
                            wire:click="showCareDetails({{ $care }})"
                            class="rounded p-1 text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Details anzeigen</span>
                        <div
                            class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-info-100 text-info-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                            </svg>
                        </div>
                    </button>
                </div>
            </div>
        @empty
            <div class="p-4 text-center text-sm text-gray-500">
                Keine Pflegeeinträge vorhanden.
                @if($selectedType)
                    <button type="button"
                            wire:click="$set('selectedType', null)"
                            class="text-primary-500 hover:text-primary-600">
                        Filter zurücksetzen
                    </button>
                @endif
            </div>
        @endforelse
    </div>
    <livewire:lawn-care.care-details-modal wire:model="isModalOpen"/>
    <livewire:lawn-care.create-care-modal/>
    <livewire:lawn-care.delete-lawn-care/>


</div>
