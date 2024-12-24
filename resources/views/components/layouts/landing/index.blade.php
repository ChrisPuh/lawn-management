<!-- resources/views/layouts/landing.blade.php -->
<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="rounded-lg border border-gray-200 bg-surface-light shadow-sm">
            <div class="px-4 py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-app-layout>
