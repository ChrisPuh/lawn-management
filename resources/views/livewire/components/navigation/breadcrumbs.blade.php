<nav aria-label="Breadcrumbs" class="flex items-center space-x-2 text-sm font-medium">
    @foreach ($segments as $key => $segment)
        <div class="flex items-center">
            @if (!$loop->first)
                <svg class="mx-2 h-5 w-5 text-primary-300" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            @endif

            @if ($segment['route'])
                <a wire:navigate href="{{ route($segment['route']) }}"
                    class="text-primary-300 transition-colors hover:text-primary-200">
                    {{ $segment['label'] }}
                </a>
            @else
                <span class="text-primary-100">
                    {{ $segment['label'] }}
                </span>
            @endif
        </div>
    @endforeach
</nav>
