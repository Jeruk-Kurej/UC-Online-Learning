<x-app-layout>
    @section('title', $user->name . ' - ' . ($user->major ?? 'Profile'))

    <div class="mx-auto max-w-[1600px] px-4 py-10 sm:px-6 lg:px-8" x-data="profileLightbox()">
        <x-ui.breadcrumbs class="mb-3" :href="route('businesses.index', ['view' => $user->directory_view])" :current="$user->name" current-class="font-semibold" />

        {{-- --- Dynamic Profile Title below Breadcrumb --- --}}
        <div class="mb-8">
            <h2 class="text-sm font-black uppercase tracking-[0.2em] text-uco-orange-500">
                {{ $user->isIntrapreneur() ? 'Intrapreneur Student' : 'Entrepreneur Student' }}
            </h2>
        </div>

        <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-12">

            @include('users.partials.show-profile')

            {{-- --- Right Side: Businesses & Jobs (8 cols) --- --}}
            <div class="space-y-10 lg:col-span-8">

                @include('users.partials.show-ventures')

                @include('users.partials.show-testimony')
            </div>

        </div>

        @include('users.partials.show-lightbox')
    </div>
</x-app-layout>
