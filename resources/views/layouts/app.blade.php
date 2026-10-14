<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Universitas Ciputra Online Learning - @yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-uco.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    @stack('meta')
    @stack('styles')
</head>
{{-- Layout body: keep background subtle and readable --}}

<body class="font-sans antialiased bg-gray-50 overflow-x-hidden">
    <div class="min-h-screen flex flex-col">
        {{-- Navigation --}}
        @include('layouts.navigation')

        {{-- Page Heading --}}
        @isset($header)
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="w-full max-w-[1600px] mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- Main Content --}}
        <main class="flex-grow">
            <div class="w-full max-w-[1600px] mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{-- Global Toast Notifications --}}
                <div class="fixed top-6 right-6 z-50 flex flex-col gap-3 items-end pointer-events-none">
                    @foreach (['success', 'error', 'status', 'warning'] as $flashType)
                        @if (session($flashType))
                            <x-flash.message :type="$flashType" :message="session($flashType)" />
                        @endif
                    @endforeach

                    @if ($errors->any())
                        <x-flash.errors />
                    @endif
                </div>

                {{-- Local JS Toast Trigger --}}
                <x-flash.toast-stack />

                {{-- Page Content --}}
                {{ $slot }}
            </div>
        </main>

        {{-- Footer --}}
        @include('layouts.footer')
    </div>

    @stack('scripts')

    <!-- instant.page untuk prefetching instan -->
    <script src="https://instant.page/5.2.0" type="module"
        integrity="sha384-jn7Qscg456X453Yc66mP3sQx50eFqL6z2J98D/c8w+xZ8L2r1n9Lh0I4Y5aW7V7m" crossorigin="anonymous">
    </script>
</body>

</html>
