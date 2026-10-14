<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Universitas Ciputra Online</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-uco.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="font-sans antialiased bg-soft-white overflow-x-hidden">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
        <div class="relative z-10">
            <a href="/" class="flex flex-col items-center gap-3">
                <img src="{{ asset('images/logo-uco.png') }}" alt="UCO Logo" class="w-20 h-20 object-contain shadow-lg">
                <h1 class="text-2xl font-bold text-soft-gray-900">UCO Platform</h1>
                <p class="text-sm text-soft-gray-600">Student & Alumni Community</p>
            </a>
        </div>

        <div
            class="relative z-10 w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-xl rounded-lg border border-soft-gray-100">
            {{ $slot }}
        </div>

        <div class="fixed top-6 right-6 z-50 flex flex-col gap-3 items-end pointer-events-none">
            {{-- Handled by dynamic script below --}}
        </div>

        {{-- Dynamic JS Toast Trigger --}}
        <x-flash.guest-toasts />

        <!-- Footer -->
        <div class="relative z-10 mt-6 text-center text-sm text-soft-gray-500">
            <p>&copy; {{ date('Y') }} UCO. All rights reserved.</p>
        </div>
    </div>
    <!-- instant.page untuk prefetching instan -->
    <script src="https://instant.page/5.2.0" type="module"
        integrity="sha384-jn7Qscg456X453Yc66mP3sQx50eFqL6z2J98D/c8w+xZ8L2r1n9Lh0I4Y5aW7V7m" crossorigin="anonymous">
    </script>
</body>

</html>
