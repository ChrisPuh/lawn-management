{{-- resources/views/livewire/feed-back/feedback-form.blade.php --}}
<div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8 bg-white rounded-lg shadow">
    <form wire:submit="submit" class="space-y-6">
        @if (session()->has('feedback-success'))
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
                            {{ session('feedback-success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('feedback-error'))
            <div class="rounded-md bg-error-light p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-error" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-error">
                            {{ session('feedback-error') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="space-y-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Art des Feedbacks</label>
                <select wire:model="type" id="type"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="bug">Problem / Bug melden</option>
                    <option value="feature">Neue Funktion vorschlagen</option>
                    <option value="improvement">Verbesserung vorschlagen</option>
                </select>
                @error('type')
                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titel</label>
                <input type="text" wire:model="title" id="title"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                       placeholder="Kurze Beschreibung in einem Satz">
                @error('title')
                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
                <textarea wire:model="description" id="description" rows="4"
                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                          placeholder="Detaillierte Beschreibung des Problems oder Ihrer Idee"></textarea>
                @error('description')
                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Feedback senden
            </button>
        </div>
    </form>
</div>
