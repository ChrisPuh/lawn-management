{{-- resources/views/components/construction-banner.blade.php --}}
<div x-data="{
        show: !$cookies.get('construction_banner_hidden'),
        hideBanner() {
            this.show = false;
            $cookies.set('construction_banner_hidden', true, '7d');
        }
     }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform -translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform -translate-y-2"
     class="relative isolate flex items-center gap-x-6 overflow-hidden bg-warning-light px-6 py-2.5 sm:px-3.5">

    <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
        <div class="flex items-center gap-x-2">
            <svg class="h-5 w-5 text-warning-dark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path
                    d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0M12 9v4M12 17h.01"/>
            </svg>
            <p class="text-sm leading-6 text-warning-dark">
                <strong class="font-semibold">Hinweis:</strong> Diese Website befindet sich noch im Aufbau.
                <span class="hidden sm:inline">Einige Funktionen sind möglicherweise noch nicht verfügbar.</span>
            </p>
        </div>
    </div>
    <button @click="hideBanner()" type="button" class="-m-3 flex-none p-3">
        <span class="sr-only">Banner schließen</span>
        <svg class="h-5 w-5 text-warning-dark" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path
                d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/>
        </svg>
    </button>
</div>
