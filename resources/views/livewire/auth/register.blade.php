<div>
    <!-- In diesem Fall brauchen wir keine zusätzlichen Container, da das Layout bereits die Struktur bereitstellt -->
    <div class="space-y-8">
        <!-- Header -->
        <div>
            <h2 class="text-center text-3xl font-bold tracking-tight text-gray-900">
                Create an Account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="{{ route('login') }}" class="font-medium text-primary-500 hover:text-primary-600">
                    Login
                </a>
            </p>
        </div>
        <form wire:submit="register">
            {{ $this->form }}

            <div class="mt-4 flex items-center justify-end">
                <a class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    href="{{ route('login') }}">
                    Already registered?
                </a>

                <button type="submit"
                    class="ml-4 inline-flex items-center rounded-md border border-transparent bg-primary-500 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-primary-600 focus:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:bg-primary-700">
                    Register
                </button>
            </div>
        </form>
    </div>
</div>
