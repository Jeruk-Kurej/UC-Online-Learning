<x-app-layout>
    @section('title', $user->name . ' - ' . ($user->major ?? 'Profile'))

    <div class="mx-auto max-w-[1600px] px-4 py-10 sm:px-6 lg:px-8" x-data="{ showLightbox: false, lightboxUrl: '' }">
        {{-- --- Breadcrumbs (matches business / intrapreneur detail pages) --- --}}
        <nav class="mb-3 flex text-sm font-medium" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li>
                    <a href="{{ route('businesses.index', ['view' => $user->directory_view]) }}" class="text-gray-400 transition hover:text-uco-orange-500">Directory</a>
                </li>
                <li class="flex items-center space-x-2">
                    <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" />
                    </svg>
                    <span class="font-semibold text-gray-900">{{ $user->name }}</span>
                </li>
            </ol>
        </nav>

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
