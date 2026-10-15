<x-app-layout>
    @section('title', $user->name . ' - ' . ($user->major ?? 'Profile'))


    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ showLightbox: false, lightboxUrl: '' }">
        {{-- Breadcrumbs (matches business / intrapreneur detail pages) --}}
        <nav class="flex mb-3 text-sm font-medium" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li>
                    <a href="{{ route('businesses.index', ['view' => $user->directory_view]) }}" class="text-gray-400 hover:text-uco-orange-500 transition">Directory</a>
                </li>
                <li class="flex items-center space-x-2">
                    <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/>
                    </svg>
                    <span class="text-gray-900 font-semibold">{{ $user->name }}</span>
                </li>
            </ol>
        </nav>

        {{-- Dynamic Profile Title below Breadcrumb --}}
        <div class="mb-8">
            <h2 class="text-sm font-black uppercase tracking-[0.2em] text-uco-orange-500">
                {{ $user->isIntrapreneur() ? 'Intrapreneur Student' : 'Entrepreneur Student' }}
            </h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            @include('users.partials.show-profile')

            {{-- Right Side: Businesses & Jobs (8 cols) --}}
            <div class="lg:col-span-8 space-y-10">

                @include('users.partials.show-ventures')

                @include('users.partials.show-testimony')
            </div>

        </div>

        @include('users.partials.show-lightbox')
    </div>
</x-app-layout>