<x-layouts.authenticated :title="$title">

    <div class="spacer flex flex-col space-y-6">
        @include('profile.partials.update-profile-information-form')
        <hr class="border-t border-gray-300">
        @include('profile.partials.update-password-form')
        <hr class="border-t border-gray-300">
        @include('profile.partials.delete-user-form')
    </div>
</x-layouts.authenticated>
