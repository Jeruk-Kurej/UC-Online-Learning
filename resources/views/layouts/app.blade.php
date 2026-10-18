<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <x-layout.head>Universitas Ciputra Online Learning - @yield('title', 'Dashboard')</x-layout.head>

    {{-- --- Scripts --- --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('meta')
    @stack('styles')
</head>
{{-- --- Layout body: keep background subtle and readable --- --}}

<body class="overflow-x-hidden bg-gray-50 font-sans antialiased">
    <div class="flex min-h-screen flex-col">
        {{-- --- Navigation --- --}}
        @include('layouts.navigation')

        {{-- --- Page Heading --- --}}
        @isset($header)
            <header class="border-b border-gray-200 bg-white shadow-sm">
                <div class="mx-auto w-full max-w-[1600px] px-4 py-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- --- Main Content --- --}}
        <main class="flex-grow">
            <div class="mx-auto w-full max-w-[1600px] px-4 py-6 sm:px-6 lg:px-8">
                {{-- --- Global Toast Notifications --- --}}
                <div class="pointer-events-none fixed right-6 top-6 z-50 flex flex-col items-end gap-3">
                    @foreach (['success', 'error', 'status', 'warning'] as $flashType)
                        @if (session($flashType))
                            <x-flash.message :type="$flashType" :message="session($flashType)" />
                        @endif
                    @endforeach

                    @if ($errors->any())
                        <x-flash.errors />
                    @endif
                </div>

                {{-- --- Local JS Toast Trigger --- --}}
                <x-flash.toast-stack />

                {{-- --- Page Content --- --}}
                {{ $slot }}
            </div>
        </main>

        {{-- --- Footer --- --}}
        @include('layouts.footer')
    </div>

    @stack('scripts')

</body>

</html>
