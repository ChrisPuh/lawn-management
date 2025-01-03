<div class="overflow-hidden rounded-lg border border-primary-200 bg-white shadow-sm">
    <div class="border-b border-primary-200 px-6 py-4">
        <h2 class="text-xl font-semibold text-gray-900">Rasenflächen Übersicht</h2>
    </div>
    <div class="grid gap-6 px-6 py-4 sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <dt class="text-sm font-medium text-gray-500">Gesamtanzahl Rasenflächen</dt>
            <dd class="text-sm text-gray-900">{{ $totalLawns }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Letzte Pflege</dt>
            <dd class="text-sm text-gray-900">{{ $lastMowedDate ?? 'Keine Pflege eingetragen' }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Nächste geplante Pflege</dt>
            <dd class="text-sm text-gray-900">Wird implementiert</dd>
        </div>
    </div>
</div>
