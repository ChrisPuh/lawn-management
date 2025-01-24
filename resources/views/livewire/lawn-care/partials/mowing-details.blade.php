{{-- mowing-details.blade.php --}}
<div class="space-y-4">
    <h3 class="text-lg font-medium text-gray-900">Mähen Details</h3>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <h4 class="text-sm font-medium text-gray-500">Schnitthöhe</h4>
            <p class="mt-1 text-sm text-gray-900">{{ $care_data['height_mm'] ?? '-' }} mm</p>
        </div>

        @if(isset($care_data['pattern']))
            <div>
                <h4 class="text-sm font-medium text-gray-500">Muster</h4>
                <p class="mt-1 text-sm text-gray-900">{{ \App\Enums\LawnCare\MowingPattern::from($care_data['pattern'])->label() }}</p>
            </div>
        @endif

        @if(isset($care_data['blade_condition']))
            <div>
                <h4 class="text-sm font-medium text-gray-500">Klingenzustand</h4>
                <p class="mt-1 text-sm text-gray-900">{{ \App\Enums\LawnCare\BladeCondition::from($care_data['blade_condition'])->label() }}</p>
            </div>
        @endif

        @if(isset($care_data['duration_minutes']))
            <div>
                <h4 class="text-sm font-medium text-gray-500">Dauer</h4>
                <p class="mt-1 text-sm text-gray-900">{{ $care_data['duration_minutes'] }} Minuten</p>
            </div>
        @endif
    </div>

    @if(isset($care_data['collected']))
        <div class="flex items-center mt-4">
            <svg class="h-5 w-5 {{ $care_data['collected'] ? 'text-green-500' : 'text-gray-400' }}" fill="none"
                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
            <span class="ml-2 text-sm text-gray-700">Schnittgut gesammelt</span>
        </div>
    @endif
</div>
