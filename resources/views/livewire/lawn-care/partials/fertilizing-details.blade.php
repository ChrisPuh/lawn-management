{{-- fertilizing-details.blade.php --}}
<div class="space-y-4">
    <h3 class="text-lg font-medium text-gray-900">Düngen Details</h3>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <h4 class="text-sm font-medium text-gray-500">Produkt</h4>
            <p class="mt-1 text-sm text-gray-900">{{ $care_data['product_name'] ?? '-' }}</p>
        </div>

        <div>
            <h4 class="text-sm font-medium text-gray-500">Menge pro m²</h4>
            <p class="mt-1 text-sm text-gray-900">{{ $care_data['amount_per_sqm'] ?? '-' }} g/m²</p>
        </div>

        @if(isset($care_data['temperature_celsius']))
            <div>
                <h4 class="text-sm font-medium text-gray-500">Temperatur</h4>
                <p class="mt-1 text-sm text-gray-900">{{ $care_data['temperature_celsius'] }}°C</p>
            </div>
        @endif

        @if(isset($care_data['weather_condition']))
            <div>
                <h4 class="text-sm font-medium text-gray-500">Wetterbedingungen</h4>
                <p class="mt-1 text-sm text-gray-900">{{ \App\Enums\LawnCare\WeatherCondition::from($care_data['weather_condition'])->label() }}</p>
            </div>
        @endif
    </div>

    @if(isset($care_data['nutrients']) && count($care_data['nutrients']) > 0)
        <div class="mt-4">
            <h4 class="text-sm font-medium text-gray-500">Nährstoffe</h4>
            <div class="mt-1 flex flex-wrap gap-2">
                @foreach($care_data['nutrients'] as $nutrient)
                    <span class="inline-flex items-center rounded-full bg-primary-100 px-3 py-0.5 text-sm font-medium text-primary-800">
                        {{ $nutrient }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    @if(isset($care_data['watered']))
        <div class="flex items-center mt-4">
            <svg class="h-5 w-5 {{ $care_data['watered'] ? 'text-green-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
            <span class="ml-2 text-sm text-gray-700">Bewässert</span>
        </div>
    @endif
</div>
