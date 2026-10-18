<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <x-layout.head>Universitas Ciputra Online</x-layout.head>

    {{-- --- Scripts --- --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="overflow-x-hidden bg-soft-white font-sans antialiased">
    <div class="relative flex min-h-screen flex-col items-center overflow-hidden pt-6 sm:justify-center sm:pt-0">
        <div class="relative z-10">
            <a href="/" class="flex flex-col items-center gap-3">
                <img src="{{ asset('images/logo-uco.png') }}" alt="UCO Logo" class="h-20 w-20 object-contain shadow-lg">
                <h1 class="text-2xl font-bold text-soft-gray-900">UCO Platform</h1>
                <p class="text-sm text-soft-gray-600">Student & Alumni Community</p>
            </a>
        </div>

        <div
             class="relative z-10 mt-6 w-full rounded-lg border border-soft-gray-100 bg-white px-6 py-8 shadow-xl sm:max-w-md">
            {{ $slot }}
        </div>

        <div class="pointer-events-none fixed right-6 top-6 z-50 flex flex-col items-end gap-3">
            {{-- --- Handled by dynamic script below --- --}}
        </div>

        {{-- --- Dynamic JS Toast Trigger --- --}}
        <x-flash.guest-toasts />

        {{-- --- Footer --- --}}
        <div class="relative z-10 mt-6 text-center text-sm text-gray-600">
            <p>&copy; {{ date('Y') }} UCO. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
