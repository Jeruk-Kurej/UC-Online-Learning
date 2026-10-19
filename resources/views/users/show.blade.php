<x-app-layout>
    @section('title', $user->name . ' - ' . ($user->major ?? 'Profile'))

    <div x-data="profileLightbox()">
        <x-ui.breadcrumbs class="mb-6" :href="route('businesses.index', ['view' => $user->directory_view])" :current="$user->name" />

        <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-3">
            @include('users.partials.show-profile')

            <div class="min-w-0 space-y-10 lg:col-span-2">
                @include('users.partials.show-ventures')

                @include('users.partials.show-testimony')
            </div>
        </div>

        @include('users.partials.show-lightbox')
    </div>
</x-app-layout>
