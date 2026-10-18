<x-app-layout>
    @section('title', 'Account')

    <div class="mx-auto max-w-3xl space-y-6">
        <x-ui.page-header title="Account" subtitle="Logged in as {{ $user->email }}." />

        @include('profile.partials.password')
    </div>
</x-app-layout>
