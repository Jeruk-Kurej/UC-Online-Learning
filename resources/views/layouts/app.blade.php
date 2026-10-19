<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <x-layout.head>@yield('title', 'UC Online Learning') | Universitas Ciputra Online Learning</x-layout.head>

    {{-- --- Scripts --- --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('meta')
    @stack('styles')
</head>

<body class="overflow-x-hidden">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-white focus:px-4 focus:py-2 focus:text-brand-900">Skip to content</a>

    <div class="flex min-h-screen flex-col">
        {{-- --- Navigation --- --}}
        @include('layouts.navigation', ['wide' => $wide])

        {{-- --- Main Content --- --}}
        <main id="main" class="flex-grow">
            {{-- --- Messages over the page (session messages first, then the ones raised by scripts) --- --}}
            <div class="toast-stack">
                @foreach (['success', 'error', 'status', 'warning'] as $flashType)
                    @if (session($flashType))
                        <x-flash.message :type="$flashType" :message="session($flashType)" />
                    @endif
                @endforeach

                @if ($errors->any())
                    <x-flash.errors />
                @endif

                <x-flash.toast-stack />
            </div>

            @if ($bleed)
                {{ $slot }}
            @else
                <div @class(['page', 'page-wide' => $wide])>
                    {{ $slot }}
                </div>
            @endif
        </main>

        {{-- --- Footer --- --}}
        @include('layouts.footer', ['wide' => $wide])
    </div>

    @stack('scripts')
</body>

</html>
