<div>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>
    @if ($errors->has('verification'))
        <div class="text-danger-dark mb-4 text-sm font-medium">
            {{ $errors->first('verification') }}
        </div>
    @endif
    @if ($verificationLinkSent)
        <div class="mb-4 text-sm font-medium text-success-dark">
            {{ __(' A new verification link has been sent to your email.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <x-primary-button wire:click="resendVerification" wire:loading.attr="disabled"
            class="inline-flex items-center rounded-md border border-transparent bg-primary-500 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-primary-600 focus:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:bg-primary-700">
            <span wire:loading.remove wire:target="resendVerification">
                {{ __('Resend Verification Email') }}
            </span>
            <span wire:loading wire:target="resendVerification">
                {{ __('Sending...') }}
            </span>
        </x-primary-button>

        <button wire:click="logout" type="button"
            class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
            {{ __('Log Out') }}
        </button>
    </div>
</div>
