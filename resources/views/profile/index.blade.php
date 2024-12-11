<!-- resources/views/profile/index.blade.php -->
<x-layouts.authenticated :title="$title">


    <div class="space-y-8">
        <!-- Profil Info Card -->
        <div class="overflow-hidden rounded-lg border border-primary-200 bg-white shadow-sm">
            <div class="flex items-center gap-6 border-b border-primary-200 px-6 py-4">
                <div
                    class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-full bg-primary-100 text-3xl font-medium text-primary-600">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ auth()->user()->name }}</h2>
                    <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <div class="grid gap-6 px-6 py-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Mitglied seit</dt>
                    <dd class="text-sm text-gray-900">{{ auth()->user()->created_at->format('d.m.Y') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Letzte Anmeldung</dt>
                    <dd class="text-sm text-gray-900">{{ auth()->user()->updated_at->format('d.m.Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Aktive Rasenflächen</dt>
                    <!-- Hier wird die Anzahl der Rasenflächen des angemeldeten Benutzers angezeigt TODO: implement  auth()->user()->lawns()->count()  -->
                    <dd class="text-sm text-gray-900">
                        daran wird noch gearbeitet
                    </dd>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div>
            <h3 class="mb-4 text-lg font-medium text-gray-900">Schnellzugriff</h3>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Profil Bearbeiten -->
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-4 rounded-lg border border-primary-200 bg-white p-6 shadow-sm transition hover:border-primary-300 hover:shadow-md">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-100">
                        <svg class="h-6 w-6 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Profil Bearbeiten</h3>
                        <p class="text-sm text-gray-500">Name, Email und weitere Einstellungen anpassen</p>
                    </div>
                </a>

                <!-- Platzhalter für zukünftige Bereiche -->
                <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-gray-50 p-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100">
                        <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-400">Weitere Einstellungen</h3>
                        <p class="text-sm text-gray-400">Demnächst verfügbar</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.authenticated>
