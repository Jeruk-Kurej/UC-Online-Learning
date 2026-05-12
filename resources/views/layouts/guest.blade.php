<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'UCO Student & Alumni') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        {{-- Manual fallback: Read manifest and inject assets --}}
        @php
            $manifestPath = public_path('build/manifest.json');
            if (file_exists($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath), true);
                $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
                $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
            }
        @endphp
        
        @if(isset($cssFile))
            <link rel="stylesheet" href="/build/{{ $cssFile }}">
        @endif
        
        @if(isset($jsFile))
            <script type="module" src="/build/{{ $jsFile }}"></script>
        @endif
    </head>
    <body class="font-sans antialiased bg-soft-white">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
            <div class="relative z-10">
                <a href="/" class="flex flex-col items-center gap-3">
                    <img src="{{ asset('images/Logo UCO.png') }}" alt="UCO Logo" class="w-20 h-20 object-contain shadow-lg">
                    <h1 class="text-2xl font-bold text-soft-gray-900">UCO Platform</h1>
                    <p class="text-sm text-soft-gray-600">Student & Alumni Community</p>
                </a>
            </div>

            <div class="relative z-10 w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-xl rounded-lg border border-soft-gray-100">
                {{ $slot }}
            </div>

            <div class="fixed top-6 right-6 z-50 flex flex-col gap-3 items-end pointer-events-none">
                {{-- Handled by dynamic script below --}}
            </div>

            {{-- Dynamic JS Toast Trigger --}}
            <div x-data="{ 
                    toasts: [], 
                    init() {
                        @if(session('success')) this.add({ detail: { message: '{{ session('success') }}', type: 'success' } }); @endif
                        @if(session('error')) this.add({ detail: { message: '{{ session('error') }}', type: 'error' } }); @endif
                        @if(session('status')) this.add({ detail: { message: '{{ session('status') }}', type: 'status' } }); @endif
                        @if($errors->any()) 
                            @foreach($errors->all() as $error)
                                this.add({ detail: { message: '{{ $error }}', type: 'error' } });
                            @endforeach
                        @endif
                    },
                    add(e) { 
                        const id = Date.now() + Math.random();
                        const { message, type } = e.detail;
                        this.toasts.push({ id, message, type: type || 'success' });
                        setTimeout(() => this.remove(id), 5000);
                    },
                    remove(id) {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }
                 }"
                 @notify.window="add($event)"
                 class="fixed top-6 right-6 z-50 flex flex-col gap-3 items-end pointer-events-none">
                
                <template x-for="toast in toasts" :key="toast.id">
                    <div x-show="true"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-[-8px]"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-[-8px]"
                         class="pointer-events-auto max-w-sm w-full p-4 rounded-xl shadow-lg flex items-start justify-between gap-3 text-white transition-all duration-300"
                         :class="{
                            'bg-emerald-600': toast.type === 'success',
                            'bg-red-600': toast.type === 'error',
                            'bg-blue-600': toast.type === 'info' || toast.type === 'status',
                            'bg-yellow-600': toast.type === 'warning'
                         }">
                        <div class="flex items-center gap-3">
                            <template x-if="toast.type === 'success'"><i class="bi bi-check-circle-fill text-lg"></i></template>
                            <template x-if="toast.type === 'error'"><i class="bi bi-exclamation-triangle-fill text-lg"></i></template>
                            <template x-if="toast.type === 'info' || toast.type === 'status'"><i class="bi bi-info-circle-fill text-lg"></i></template>
                            <template x-if="toast.type === 'warning'"><i class="bi bi-exclamation-circle-fill text-lg"></i></template>
                            <span class="text-sm font-semibold" x-text="toast.message"></span>
                        </div>
                        <button @click="remove(toast.id)" class="text-white/80 hover:text-white transition-colors">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </template>
            </div>

            <!-- Footer -->
            <div class="relative z-10 mt-6 text-center text-sm text-soft-gray-500">
                <p>&copy; {{ date('Y') }} UCO. All rights reserved.</p>
            </div>
        </div>
    </body>
</html>
