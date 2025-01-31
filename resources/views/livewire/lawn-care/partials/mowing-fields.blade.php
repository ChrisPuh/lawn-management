{{-- mowing-fields.blade.php --}}
@props([
    'isEditing' => false
])
<div class="space-y-6">
    <div>
        <label class="block text-sm font-medium text-gray-700">Schnitthöhe (mm)</label>
        <input
            type="number"
            wire:model="care_data.height_mm"
            wire:disabled="{{ !$isEditing }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 disabled:bg-gray-100"
        >
        @error('care_data.height_mm') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Muster</label>
        <select
            wire:model="care_data.pattern"
            wire:disabled="{{ !$isEditing }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 disabled:bg-gray-100"
        >
            <option value="">Bitte wählen...</option>
            @foreach($mowingPatterns as $pattern)
                <option value="{{ $pattern->value }}">{{ $pattern->label() }}</option>
            @endforeach
        </select>
        @error('care_data.pattern') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Klingenzustand</label>
        <select
            wire:model="care_data.blade_condition"
            wire:disabled="{{ !$isEditing }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 disabled:bg-gray-100"
        >
            <option value="">Bitte wählen...</option>
            @foreach($bladeConditions as $condition)
                <option value="{{ $condition->value }}">{{ $condition->label() }}</option>
            @endforeach
        </select>
        @error('care_data.blade_condition') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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

    <div class="flex items-center">
        <input
            type="checkbox"
            wire:model="care_data.collected"
            wire:disabled="{{ !$isEditing }}"
            class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 disabled:bg-gray-100"
        >
        <label class="ml-2 block text-sm text-gray-700">Schnittgut gesammelt</label>
        @error('care_data.collected') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
</div>
