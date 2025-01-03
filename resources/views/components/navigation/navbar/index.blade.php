<nav class="bg-primary-500 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <!-- Left: Logo and Main Nav -->
            <div class="flex items-center space-x-12">
                <a href="/" class="text-xl font-semibold">{{ config('app.name') }}</a>

                <!-- auth routes -->
                @auth
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('dashboard') }}" class="hover:text-primary-200">Dashboard</a>

                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('lawn.index') }}" class="hover:text-primary-200">Lawns</a>

                    </div>
                @endauth
            </div>

            <!-- Right: Auth -->
            <div class="flex items-center space-x-12">
                @auth
                    <livewire:components.navigation.nav-menu-dropdown />
                @else
                    <div class="flex items-center space-x-4">

                        <a href="{{ route('welcome') }}" class="hover:text-primary-200">Home</a>
                        <a href="{{ route('about') }}" class="hover:text-primary-200">About</a>
                        <a href="{{ route('features') }}" class="hover:text-primary-200">Features</a>
                        <a href="{{ route('terms') }}" class="hover:text-primary-200">Terms</a>
                        <a href="{{ route('contact') }}" class="hover:text-primary-200">Contact</a>
                        <a href="{{ route('privacy') }}" class="hover:text-primary-200">Privacy</a>
                    </div>
                @endauth


                <div class="">
                    @auth
                        <livewire:components.navigation.user-menu-dropdown />
                    @else
                        <a href="{{ route('login') }}" class="hover:text-primary-200">Login</a>
                        <a href="{{ route('register') }}"
                            class="rounded bg-primary-600 px-4 py-2 hover:bg-primary-700">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>
