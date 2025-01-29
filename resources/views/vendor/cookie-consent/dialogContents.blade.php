{{-- resources/views/vendor/cookie-consent/dialogContents.blade.php --}}
@if($cookieConsentConfig['enabled'] && ! $alreadyConsentedWithCookies)
    <div class="w-full bg-primary-600 relative"
         x-data="{ show: true }"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         role="dialog"
         aria-labelledby="cookie-consent-banner">
        <div class="max-w-screen-xl mx-auto px-4 py-3 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="w-0 flex-1 flex items-center min-w-0">
                    <p class="text-white font-medium" id="cookie-consent-banner">
                        {{ config('cookie-consent.texts.message') }}
                        <a href="{{ route('cookie-policy') }}" class="underline hover:text-white/90">
                            {{ config('cookie-consent.texts.learn_more') }}
                        </a>
                    </p>
                </div>
                <div class="flex-shrink-0 w-full sm:w-auto flex items-center gap-4 justify-end">
                    <button
                        class="js-cookie-consent-agree cookie-consent__agree flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium text-primary-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors duration-150"
                        @click="show = false"
                    >
                        {{ config('cookie-consent.texts.agree') }}
                    </button>
                    <button
                        class="text-sm text-white hover:text-white/90 underline focus:outline-none"
                        @click="show = false"
                    >
                        {{ config('cookie-consent.texts.deny') }}
                    </button>
                </div>
            </div>
    </div>
@endif
