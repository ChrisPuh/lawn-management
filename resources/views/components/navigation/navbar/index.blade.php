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
                @endauth
            </div>

            <!-- Right: Auth -->
            <div class="flex items-center space-x-12">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('welcome') }}" class="hover:text-primary-200">Home</a>
                    <a href="{{ route('about') }}" class="hover:text-primary-200">About</a>
                    <a href="{{ route('features') }}" class="hover:text-primary-200">Features</a>
                </div>
                <div class="">
                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="rounded bg-primary-600 px-4 py-2 text-white hover:bg-primary-700">
                                Logout
                            </button>
                        </form>
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
