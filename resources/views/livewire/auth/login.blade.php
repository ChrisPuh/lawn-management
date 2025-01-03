<div>
    <!-- In diesem Fall brauchen wir keine zusätzlichen Container, da das Layout bereits die Struktur bereitstellt -->
    <div class="space-y-8">
        <!-- Header -->
        <div>
            <h2 class="text-center text-3xl font-bold tracking-tight text-gray-900">
                Sign in to your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="{{ route('register') }}" class="font-medium text-primary-500 hover:text-primary-600">
                    create a new account
                </a>
            </p>
        </div>

        <!-- Form -->
        <form wire:submit="login" class="mt-8 space-y-6">
            <div class="space-y-4 rounded-md shadow-sm">
                {{ $this->form }}
            </div>

            @if ($canResetPassword)
                <div class="flex items-center justify-between">
                    <div class="text-sm">
                        <a href="{{ route('password.request') }}"
                            class="font-medium text-primary-500 hover:text-primary-600">
                            Forgot your password?
                        </a>
                    </div>
                </div>
            @endif

            <div>
                <button type="submit"
                    class="group relative flex w-full justify-center rounded-md border border-transparent bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-400 focus:ring-offset-2">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-primary-300 group-hover:text-primary-400"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                    Sign in
                </button>
            </div>
        </form>
    </div>
</div>
