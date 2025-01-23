{{-- fertilizing-fields.blade.php --}}
<div class="space-y-6">
    <div>
        <label class="block text-sm font-medium text-gray-700">Produktname</label>
        <input
            type="text"
            wire:model="care_data.product_name"
            :disabled="!isEditing"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 disabled:bg-gray-100"
        >
        @error('care_data.product_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Menge pro m²</label>
        <input
            type="number"
            step="0.01"
            wire:model="care_data.amount_per_sqm"
            :disabled="!isEditing"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 disabled:bg-gray-100"
        >
        @error('care_data.amount_per_sqm') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Temperatur (°C)</label>
        <input
            type="number"
            wire:model="care_data.temperature_celsius"
            :disabled="!isEditing"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 disabled:bg-gray-100"
        >
        @error('care_data.temperature_celsius') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Wetterbedingungen</label>
        <select
            wire:model="care_data.weather_condition"
            :disabled="!isEditing"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 disabled:bg-gray-100"
        >
            <option value="">Bitte wählen...</option>
            @foreach($weatherConditions as $condition)
                <option value="{{ $condition->value }}">{{ $condition->label() }}</option>
            @endforeach
        </select>
        @error('care_data.weather_condition') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="flex items-center">
        <input
            type="checkbox"
            wire:model="care_data.watered"
            :disabled="!isEditing"
            class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 disabled:bg-gray-100"
        >
        <label class="ml-2 block text-sm text-gray-700">Bewässert</label>
        @error('care_data.watered') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
</div>
