<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <!-- Dropdown Button -->
    <button @click="open = !open" type="button"
        class="inline-flex items-center gap-x-1 text-gray-700 hover:text-primary-600 focus:outline-none">
        Menu
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute left-0 z-10 mt-2 w-48 origin-top-left rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        role="menu">
        @auth
            <a href="{{ route('welcome') }}"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700" role="menuitem">
                Home
            </a>
            <a href="{{ route('about') }}"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700" role="menuitem">
                About
            </a>
            <a href="{{ route('features') }}"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700" role="menuitem">
                Features
            </a>
            <a href="{{ route('terms') }}"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700" role="menuitem">
                Terms
            </a>
            <a href="{{ route('contact') }}"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700" role="menuitem">
                Contact
            </a>
            <a href="{{ route('privacy') }}"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700" role="menuitem">
                Privacy
            </a> <a href="{{ route('feedback') }}"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700" role="menuitem">
                Feedback
            </a>

        @endauth
    </div>
</div>
