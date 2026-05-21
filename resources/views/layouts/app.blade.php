<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Universitas Ciputra Online - @yield('title', 'Dashboard')</title>
        <link rel="icon" type="image/png" href="{{ asset('images/Logo UCO.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />

        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        
        <!-- FontAwesome Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- GSAP Animations -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- {{-- Fallback for built assets (used when Vite manifest exists) --}}
        @php
            $manifestPath = public_path('build/manifest.json');
            if (file_exists($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath), true);
                $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
                $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
            }
        @endphp -->

        <!-- @if(!empty($cssFile))
            <link rel="stylesheet" href="/build/{{ $cssFile }}">
        @endif

        @if(!empty($jsFile))
            <script type="module" src="/build/{{ $jsFile }}"></script>
        @endif -->

        <style>
            [x-cloak] { display: none !important; }
        </style>

        @stack('meta')
        @stack('styles')
    </head>
    {{-- Layout body: keep background subtle and readable --}}
    <body class="font-sans antialiased bg-gray-50">
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
                        @if (session('success'))
                            <div x-data="{ show: true }" 
                                 x-show="show" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-[-10px] scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 x-init="setTimeout(() => show = false, 4000)" 
                                 class="pointer-events-auto max-w-sm w-full p-3.5 bg-white/95 backdrop-blur-md rounded-2xl border border-slate-100/90 shadow-[0_20px_40px_-5px_rgba(0,0,0,0.08),0_1px_4px_rgba(0,0,0,0.02)] flex items-center justify-between gap-3 text-slate-800 transition-all duration-300" 
                                 role="alert">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-xl flex-shrink-0 bg-emerald-50 text-emerald-600 ring-4 ring-emerald-500/10">
                                        <i class="bi bi-check-circle-fill text-base"></i>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 leading-snug">{{ session('success') }}</span>
                                </div>
                                <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-50 flex-shrink-0">
                                    <i class="bi bi-x-lg text-xs"></i>
                                </button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div x-data="{ show: true }" 
                                 x-show="show" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-[-10px] scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 x-init="setTimeout(() => show = false, 4000)" 
                                 class="pointer-events-auto max-w-sm w-full p-3.5 bg-white/95 backdrop-blur-md rounded-2xl border border-red-100/90 shadow-[0_20px_40px_-5px_rgba(0,0,0,0.08),0_1px_4px_rgba(0,0,0,0.02)] flex items-center justify-between gap-3 text-slate-800 transition-all duration-300" 
                                 role="alert">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-xl flex-shrink-0 bg-red-50 text-red-600 ring-4 ring-red-500/10">
                                        <i class="bi bi-exclamation-triangle-fill text-base"></i>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 leading-snug">{{ session('error') }}</span>
                                </div>
                                <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-50 flex-shrink-0">
                                    <i class="bi bi-x-lg text-xs"></i>
                                </button>
                            </div>
                        @endif

                        @if (session('status'))
                            <div x-data="{ show: true }" 
                                 x-show="show" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-[-10px] scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 x-init="setTimeout(() => show = false, 4000)" 
                                 class="pointer-events-auto max-w-sm w-full p-3.5 bg-white/95 backdrop-blur-md rounded-2xl border border-slate-100/90 shadow-[0_20px_40px_-5px_rgba(0,0,0,0.08),0_1px_4px_rgba(0,0,0,0.02)] flex items-center justify-between gap-3 text-slate-800 transition-all duration-300" 
                                 role="alert">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-xl flex-shrink-0 bg-blue-50 text-blue-600 ring-4 ring-blue-500/10">
                                        <i class="bi bi-info-circle-fill text-base"></i>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 leading-snug">{{ session('status') }}</span>
                                </div>
                                <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-50 flex-shrink-0">
                                    <i class="bi bi-x-lg text-xs"></i>
                                </button>
                            </div>
                        @endif

                        @if (session('warning'))
                            <div x-data="{ show: true }" 
                                 x-show="show" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-[-10px] scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 x-init="setTimeout(() => show = false, 4000)" 
                                 class="pointer-events-auto max-w-sm w-full p-3.5 bg-white/95 backdrop-blur-md rounded-2xl border border-slate-100/90 shadow-[0_20px_40px_-5px_rgba(0,0,0,0.08),0_1px_4px_rgba(0,0,0,0.02)] flex items-center justify-between gap-3 text-slate-800 transition-all duration-300" 
                                 role="alert">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-xl flex-shrink-0 bg-yellow-50 text-yellow-600 ring-4 ring-yellow-500/10">
                                        <i class="bi bi-exclamation-circle-fill text-base"></i>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 leading-snug">{{ session('warning') }}</span>
                                </div>
                                <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-50 flex-shrink-0">
                                    <i class="bi bi-x-lg text-xs"></i>
                                </button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div x-data="{ show: true }" 
                                 x-show="show" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-[-10px] scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 x-init="setTimeout(() => show = false, 7000)" 
                                 class="pointer-events-auto max-w-md w-full p-4 bg-white/95 backdrop-blur-md rounded-2xl border border-red-100/90 shadow-[0_20px_40px_-5px_rgba(0,0,0,0.08),0_1px_4px_rgba(0,0,0,0.02)] flex items-start justify-between gap-3 text-slate-800 transition-all duration-300" 
                                 role="alert">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-xl flex-shrink-0 bg-red-50 text-red-600 ring-4 ring-red-500/10 mt-0.5">
                                        <i class="bi bi-exclamation-triangle-fill text-base"></i>
                                    </div>
                                    <div class="flex flex-col gap-1 flex-1 min-w-0">
                                        <span class="text-xs font-bold text-red-800 leading-snug">Validation Error</span>
                                        <ul class="text-[11px] text-slate-500 font-medium list-disc ml-4 space-y-0.5 mt-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-50 flex-shrink-0 mt-0.5">
                                    <i class="bi bi-x-lg text-xs"></i>
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Local JS Toast Trigger --}}
                    <div x-data="{ 
                            toasts: [], 
                            add(e) { 
                                const id = Date.now();
                                const { message, type } = e.detail;
                                this.toasts.push({ id, message, type: type || 'success' });
                                
                                // Limit stack to maximum 3 toasts (removing the oldest)
                                if (this.toasts.length > 3) {
                                    this.toasts.shift();
                                }
                                
                                setTimeout(() => this.remove(id), 4000);
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
                                 x-transition:enter-start="opacity-0 translate-y-[-10px] scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="pointer-events-auto max-w-sm w-full p-3.5 bg-white/95 backdrop-blur-md rounded-2xl border border-slate-100/90 shadow-[0_20px_40px_-5px_rgba(0,0,0,0.08),0_1px_4px_rgba(0,0,0,0.02)] flex items-center justify-between gap-3 text-slate-800 transition-all duration-300">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-xl flex-shrink-0"
                                         :class="{
                                            'bg-emerald-50 text-emerald-600 ring-4 ring-emerald-500/10': toast.type === 'success',
                                            'bg-red-50 text-red-600 ring-4 ring-red-500/10': toast.type === 'error',
                                            'bg-blue-50 text-blue-600 ring-4 ring-blue-500/10': toast.type === 'info' || toast.type === 'status',
                                            'bg-yellow-50 text-yellow-600 ring-4 ring-yellow-500/10': toast.type === 'warning'
                                         }">
                                        <template x-if="toast.type === 'success'"><i class="bi bi-check-circle-fill text-base"></i></template>
                                        <template x-if="toast.type === 'error'"><i class="bi bi-exclamation-triangle-fill text-base"></i></template>
                                        <template x-if="toast.type === 'info' || toast.type === 'status'"><i class="bi bi-info-circle-fill text-base"></i></template>
                                        <template x-if="toast.type === 'warning'"><i class="bi bi-exclamation-circle-fill text-base"></i></template>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 leading-snug" x-text="toast.message"></span>
                                </div>
                                <button @click="remove(toast.id)" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-50 flex-shrink-0">
                                    <i class="bi bi-x-lg text-xs"></i>
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- Page Content --}}
                    {{ $slot }}
                </div>
            </main>

            {{-- Footer --}}
            @include('layouts.footer')
        </div>

        @stack('scripts')
    </body>
</html>
