<!-- resources/views/layouts/authenticated.blade.php -->
@props(['title'])

<x-app-layout>
    <!-- Page Header -->
    <div class="bg-gradient-to-b from-primary-500 to-primary-400 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->

            <div class="py-4">
                <livewire:components.navigation.breadcrumbs />

            </div>


            <div class="flex items-center justify-between py-6">
                <!-- Title Section -->
                <div class="flex-1">
                    <h1 class="text-2xl font-bold tracking-tight">
                        {{ $title }}
                    </h1>
                </div>

                <!-- Action Buttons -->
                @isset($actions)
                    <div class="flex items-center gap-3">
                        {{ $actions }}
                    </div>
                @endisset
            </div>
        </div>
    </div>


    <!-- Main Content -->
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div
            class="rounded-lg bg-surface-light dark:border-primary-700 dark:bg-primary-900">
            <div class="">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-app-layout>
