<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cookie Policy') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <section>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Was sind Cookies?') }}</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Cookies sind kleine Textdateien, die beim Besuch einer Website auf Ihrem Computer
                            gespeichert werden.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Welche Cookies verwenden wir?') }}</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <h4 class="font-medium text-gray-700">Technisch notwendige Cookies</h4>
                                <ul class="mt-2 text-sm text-gray-600 list-disc list-inside">
                                    <li>Session Cookie (XSRF-TOKEN): Zur Sicherheit und Authentifizierung</li>
                                    <li>Laravel Session: Für die grundlegende Webseitenfunktionalität</li>
                                    <li>Cookie Consent: Speichert Ihre Cookie-Präferenzen</li>
                                </ul>
                            </div>

                            @if(config('services.google.analytics'))
                                <div>
                                    <h4 class="font-medium text-gray-700">Analytics Cookies (optional)</h4>
                                    <ul class="mt-2 text-sm text-gray-600 list-disc list-inside">
                                        <li>Google Analytics: Zur Analyse der Webseitennutzung</li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </section>

                    <section>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Ihre Rechte') }}</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Sie haben das Recht, Ihre Cookie-Einstellungen jederzeit zu ändern. Die technisch
                            notwendigen Cookies können nicht deaktiviert werden,
                            da sie für den Betrieb der Website erforderlich sind.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Kontakt') }}</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Bei Fragen zu unseren Cookies können Sie uns unter [Ihre Kontakt-Email] erreichen.
                        </p>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
