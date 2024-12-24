<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <button @click="open = !open" type="button"
        class="inline-flex items-center gap-x-1 rounded bg-primary-600 px-4 py-2 text-sm text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
        {{ auth()->user()->name }}
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="open" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 z-10 mt-2 w-64 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        role="menu">
        <!-- User Info Section -->
        <div class="border-b border-gray-200 px-4 py-3">
            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
            <p class="truncate text-sm text-gray-500">{{ auth()->user()->email }}</p>
        </div>

        <!-- Menu Items -->
        <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
            role="menuitem">
            Profil
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem">
                Logout
            </button>
        </form>
    </div>
</div>
