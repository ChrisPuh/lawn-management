<!-- resources/views/components/layouts/landing.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Lawn Management') }} - {{ $title ?? '' }}</title>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @livewireStyles
    @filamentStyles
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex min-h-screen flex-col bg-background-light font-sans antialiased dark:bg-gray-900">
    <!-- Navigation -->
    <x-navigation.navbar />

    <!-- Main Content -->
    <main class="w-full flex-1">
        <!-- Container for content width control -->
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Content wrapper with background -->
            <div class="rounded-lg border border-gray-200 bg-surface-light shadow-sm">
                <div class="px-4 py-6 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <x-navigation.footer />

    @livewireScripts
    @filamentScripts

</body>

</html>
