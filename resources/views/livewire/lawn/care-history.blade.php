<!-- resources/views/livewire/lawn/care-history.blade.php -->
<div>
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-medium text-gray-900">Pflegehistorie</h3>
        <button
            wire:click="planNextCare"
            class="inline-flex items-center rounded-md bg-primary-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nächste Pflege
        </button>
    </div>

    <div class="space-y-3">
        @forelse($recentActivities as $activity)
            <div class="rounded-md bg-gray-50 px-4 py-3">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-900">
                            {{ $activity->type->pastTense() }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $activity->performed_at?->format('d.m.Y') }}
                        </div>
                    </div>
                    <button
                        wire:click="recordCare('{{ $activity->type->value }}')"
                        class="inline-flex items-center rounded-md bg-primary-100 px-3 py-2 text-sm font-medium text-primary-700 hover:bg-primary-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500"
                    >
                        {{ $activity->type->actionLabel() }}
                    </button>
                </div>
            </div>
        @empty
            <div class="text-sm text-gray-500 text-center py-4">
                Noch keine Pflegeaktivitäten vorhanden
            </div>
        @endforelse
    </div>
</div>
