{{-- watering-fields.blade.php --}}
@props([
    'isEditing' => false
])
<div class="space-y-6">
    <div>
        <label class="block text-sm font-medium text-gray-700">Wassermenge (Liter)</label>
        <input
            type="number"
            step="0.01"
            wire:model="care_data.amount_liters"
            wire:disabled="{{ !$isEditing }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 disabled:bg-gray-100"
        >
        @error('care_data.amount_liters') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Dauer (Minuten)</label>
        <input
            type="number"
            wire:model="care_data.duration_minutes"
            wire:disabled="{{ !$isEditing }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 disabled:bg-gray-100"
        >
        @error('care_data.duration_minutes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Methode</label>
        <select
            wire:model="care_data.method"
            wire:disabled="{{ !$isEditing }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 disabled:bg-gray-100"
        >
            <option value="">Bitte wählen...</option>
            @foreach($wateringMethods as $method)
                <option value="{{ $method->value }}">{{ $method->label() }}</option>
            @endforeach
        </select>
        @error('care_data.method') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Tageszeit</label>
        <select
            wire:model="care_data.time_of_day"
            wire:disabled="{{ !$isEditing }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 disabled:bg-gray-100"
        >
            <option value="">Bitte wählen...</option>
            @foreach($timeOfDay as $time)
                <option value="{{ $time->value }}">{{ $time->label() }}</option>
            @endforeach
        </select>
        @error('care_data.time_of_day') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Temperatur (°C)</label>
        <input
            type="number"
            wire:model="care_data.temperature_celsius"
            wire:disabled="{{ !$isEditing }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 disabled:bg-gray-100"
        >
        @error('care_data.temperature_celsius') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Wetterbedingungen</label>
        <select
            wire:model="care_data.weather_condition"
            wire:disabled="{{ !$isEditing }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 disabled:bg-gray-100"
        >
            <option value="">Bitte wählen...</option>
            @foreach($weatherConditions as $condition)
                <option value="{{ $condition->value }}">{{ $condition->label() }}</option>
            @endforeach
        </select>
        @error('care_data.weather_condition') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
</div>
