<!-- resources/views/layouts/app.blade.php -->
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

        /* Add this to prevent initial Alpine.js rendering flicker */
        [x-cloak] * {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        body.loading * {
            opacity: 0;
        }

        /* Cookie consent transitions */
        .cookie-consent-enter-active,
        .cookie-consent-leave-active {
            transition: all 0.3s ease;
        }

        .cookie-consent-enter-from,
        .cookie-consent-leave-to {
            opacity: 0;
            transform: translateY(-30px);
        }
    </style>

    @livewireStyles
    @filamentStyles
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Prevent Alpine.js flickering
        document.addEventListener('alpine:init', () => {
            document.body.classList.remove('loading');
        });
    </script>
    @stack('scripts')
</head>

<body class="loading flex min-h-screen flex-col bg-background-light font-sans antialiased dark:bg-gray-900">

<!-- Navigation -->
<x-navigation.navbar/>

<!-- Cookie Consent -->
@include('cookie-consent::index')

<!-- Construction Banner -->
<livewire:construction-banner />

<!-- Main Content -->
<main class="w-full flex-1">

    {{ $slot }}
</main>

<!-- Footer -->
<x-navigation.footer/>

@livewireScripts
@filamentScripts
</body>

</html>
