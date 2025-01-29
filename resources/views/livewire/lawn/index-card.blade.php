<!-- resources/views/livewire/lawn/index-card.blade.php -->
<div class="flex flex-col rounded-lg border border-primary-200 bg-white shadow-sm">
    <div class="flex-1 p-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">{{ $lawn->name }}</h3>
            <span class="inline-flex items-center rounded-full bg-nature-grass-healthy px-2.5 py-0.5 text-xs font-medium text-white">
                {{ $lawn->type?->label() ?? 'Nicht spezifiziert' }}
            </span>
        </div>
        <dl class="mt-4 grid grid-cols-2 gap-4">
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
                <dd class="text-sm text-gray-900">{{ $lawn->grass_seed?->label() ?? 'Nicht angegeben' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Letzte Pflege</dt>
                <dd class="text-sm text-gray-900">
                    @if ($careDate)
                        {{ $careDate['type'] }} ({{ $careDate['date'] }})
                    @else
                        Keine Pflege
                    @endif
                </dd>
            </div>
        </dl>
    </div>
    <div class="flex divide-x divide-primary-200 border-t border-primary-200">
        <a href="{{ route('lawn.show', $lawn) }}"
            class="flex flex-1 items-center justify-center py-3 text-sm font-medium text-primary-600 hover:bg-primary-50"
        >
            Details
        </a>
        <a
            href="{{route('lawn.edit', $lawn)}}"
            class="flex flex-1 items-center justify-center py-3 text-sm font-medium text-primary-600 hover:bg-primary-50"
        >
            Bearbeiten
        </a>
    </div>
</div>
