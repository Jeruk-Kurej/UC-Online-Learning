<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <x-layout.head>Universitas Ciputra Online</x-layout.head>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="overflow-x-hidden">
    <div class="flex min-h-screen flex-col items-center px-4 py-10 sm:justify-center">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ asset('images/logo-uco-128.webp') }}" alt="" class="h-14 w-14 object-contain">
            <span class="font-display text-2xl font-semibold text-brand-900">UC Online Learning</span>
        </a>

        <main class="card mt-6 w-full max-w-md">
            {{ $slot }}
        </main>

        <x-flash.guest-toasts />

        <p class="mt-6 text-sm text-gray-600">&copy; {{ date('Y') }} Universitas Ciputra Online Learning</p>
    </div>
</body>

</html>
