<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Lawn Management') }} - {{ $title ?? '' }}</title>

    <!-- Favicon - Use secure_asset() -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ secure_asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ secure_asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ secure_asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ secure_asset('site.webmanifest') }}">

    <!-- Meta Tags for HTTPS -->
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <style>
        [x-cloak] {
            display: none !important;
        }

        [x-cloak] * {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        body.loading * {
            opacity: 0;
        }

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

    <!-- Use HTTPS for external resources -->
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
<livewire:construction-banner/>

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
