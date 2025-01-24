{{-- watering-details.blade.php --}}
<div class="space-y-4">
    <h3 class="text-lg font-medium text-gray-900">Bewässerung Details</h3>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <h4 class="text-sm font-medium text-gray-500">Wassermenge</h4>
            <p class="mt-1 text-sm text-gray-900">{{ $care_data['amount_liters'] ?? '-' }} Liter</p>
        </div>

        <div>
            <h4 class="text-sm font-medium text-gray-500">Dauer</h4>
            <p class="mt-1 text-sm text-gray-900">{{ $care_data['duration_minutes'] ?? '-' }} Minuten</p>
        </div>

        @if(isset($care_data['method']))
            <div>
                <h4 class="text-sm font-medium text-gray-500">Methode</h4>
                <p class="mt-1 text-sm text-gray-900">{{ \App\Enums\LawnCare\WateringMethod::from($care_data['method'])->label() }}</p>
            </div>
        @endif

        @if(isset($care_data['time_of_day']))
            <div>
                <h4 class="text-sm font-medium text-gray-500">Tageszeit</h4>
                <p class="mt-1 text-sm text-gray-900">{{ \App\Enums\LawnCare\TimeOfDay::from($care_data['time_of_day'])->label() }}</p>
            </div>
        @endif

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
</div>
