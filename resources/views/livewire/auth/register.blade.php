<div>
    <div class="space-y-8">
        <!-- Session based notifications -->
        @if (session('status'))
            <div class="rounded-md bg-success-light p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-success" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-success">
                            {{ session('status') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div>
            <h2 class="text-center text-3xl font-bold tracking-tight text-gray-900">
                {{ $registrationEnabled ? 'Account erstellen' : 'Warteliste' }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Oder
                <a href="{{ route('login') }}" class="font-medium text-primary-500 hover:text-primary-600">
                    Login
                </a>
                @if (!$registrationEnabled)
                    <span class="block mt-2 text-sm">
                        Die Registrierung ist aktuell nur über die Warteliste möglich.
                    </span>
                @endif
            </p>
        </div>

        <form wire:submit="submit">
            {{ $this->form }}

            <div class="mt-4 flex items-center justify-end">
                <a class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                   href="{{ route('login') }}">
                    Bereits registriert?
                </a>

                <button type="submit"
                        class="ml-4 inline-flex items-center rounded-md border border-transparent bg-primary-500 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-primary-600 focus:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:bg-primary-700">
                    {{ $registrationEnabled ? 'Registrieren' : 'Auf die Warteliste' }}
                </button>
            </div>
        </form>

        @if (!$registrationEnabled)
            <p class="mt-6 text-center text-sm text-gray-500">
                Wir informieren Sie per E-Mail, sobald ein Platz für Sie frei ist.
            </p>
        @endif
    </div>
</div>
