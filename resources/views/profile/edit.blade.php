<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-[var(--text-primary)]">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="glass p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="glass p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="glass p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
