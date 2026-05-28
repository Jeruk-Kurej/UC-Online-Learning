<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Universitas Ciputra Online</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-uco.png') }}">
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
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
    
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="antialiased bg-soft-white">
    <div class="h-screen flex flex-col overflow-hidden relative">
        {{-- Main Content --}}
        <main class="flex-1 flex items-center relative overflow-hidden z-10">
            <div class="max-w-6xl mx-auto px-8 w-full relative z-10">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    {{-- Left: Content --}}
                    <div class="space-y-6 relative reveal-on-scroll">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-soft-gray-200 rounded-full shadow-sm">
                            <div class="w-2 h-2 bg-gradient-to-r from-uco-orange-400 to-uco-yellow-400 rounded-full"></div>
                            <span class="text-xs font-semibold text-soft-gray-700">For UC Students & Alumni</span>
                        </div>
                        
                        <h1 class="text-5xl font-bold text-soft-gray-900 leading-tight tracking-tight">
                            Business showcase<br>
                            for <span class="text-soft-gray-900">UC Online Learning Students</span>
                        </h1>
                        
                        <p class="text-lg text-soft-gray-600 leading-relaxed max-w-lg">
                            Connect with Universitas Ciputra's professional community. Discover businesses, explore achievements, showcase products, and grow your network.
                        </p>
                        
                        {{-- Feature Pills --}}
                        <div class="flex flex-wrap gap-3 pt-2">
                            <div class="px-4 py-2 bg-white border border-soft-gray-200 rounded-lg text-sm font-medium text-soft-gray-700 shadow-sm hover:shadow-md transition-shadow">
                                🏢 Business Profiles
                            </div>
                            <div class="px-4 py-2 bg-white border border-soft-gray-200 rounded-lg text-sm font-medium text-soft-gray-700 shadow-sm hover:shadow-md transition-shadow">
                                📦 Product Catalog
                            </div>
                            <div class="px-4 py-2 bg-white border border-soft-gray-200 rounded-lg text-sm font-medium text-soft-gray-700 shadow-sm hover:shadow-md transition-shadow">
                                🤝 Alumni Network
                            </div>
                        </div>
                    </div>
                    
                    {{-- Right: Login Form --}}
                    <div class="bg-white/90 backdrop-blur-2xl border border-white/60 rounded-[2.5rem] p-10 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.08)] relative overflow-hidden reveal-on-scroll" style="transition-delay: 200ms;">
                        {{-- Decorative corner elements --}}
                        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-uco-orange-100 to-uco-yellow-100 rounded-bl-[4rem] opacity-50"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-uco-yellow-100 to-uco-orange-100 rounded-tr-[4rem] opacity-40"></div>
                        
                        <div class="mb-8 relative z-10">
                            <h2 class="text-3xl font-black text-gray-900 mb-1.5 tracking-tight">Sign in</h2>
                            <p class="text-sm font-medium text-gray-500">Access your showcase hub</p>
                        </div>
                        

                        
                        <form method="POST" action="/login" class="space-y-5 relative z-10">
                            @csrf
                            
                            <div>
                                <label for="email" class="form-label-custom">Email</label>
                                <input id="email" 
                                       type="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus 
                                       autocomplete="username"
                                       class="form-input-custom @error('email') border-red-500 @enderror"
                                       placeholder="name@example.com">
                                @error('email')
                                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="password" class="form-label-custom">Password</label>
                                <input id="password" 
                                       type="password" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       class="form-input-custom @error('password') border-red-500 @enderror"
                                       placeholder="Enter your password">
                                @error('password')
                                    <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <button type="submit" 
                                    class="w-full py-3 bg-uco-orange-500 hover:bg-uco-orange-600 text-white text-sm font-bold tracking-wide rounded-[7px] shadow-sm transition-all duration-200 uppercase">
                                Sign in to Dashboard
                            </button>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-soft-gray-200"></div>
                                </div>
                                <div class="relative flex justify-center text-xs">
                                    <span class="bg-white/90 px-2 text-gray-400 font-medium">or continue with</span>
                                </div>
                            </div>

                            <a href="{{ route('auth.google.redirect') }}"
                               class="w-full flex items-center justify-center gap-3 py-3 px-4 bg-white text-gray-700 text-sm font-semibold rounded-[7px] border border-gray-200 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900 transition-all duration-200 shadow-sm">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                </svg>
                                <span>Sign in with Google</span>
                            </a>
                        </form>
                        
                        {{-- Back to Home Button --}}
                        <div class="mt-6 pt-6 border-t border-gray-100 relative z-10">
                            @if(auth()->check())
                                <a href="{{ route('businesses.index') }}" 
                                   class="block w-full py-3 text-center bg-gray-50 text-gray-600 text-sm font-semibold rounded-[7px] border border-gray-200 hover:bg-gray-100 hover:text-gray-900 transition-all duration-200">
                                    Browse Businesses
                                </a>
                            @else
                                <a href="{{ route('featured') }}" 
                                   class="block w-full py-3 text-center bg-gray-50 text-gray-600 text-sm font-semibold rounded-[7px] border border-gray-200 hover:bg-gray-100 hover:text-gray-900 transition-all duration-200">
                                    Browse as Guest
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Floating Decorative Elements -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <!-- Floating Circle 1 -->
                <div class="absolute top-20 left-[10%] w-16 h-16 border-2 border-uco-orange-300 rounded-full opacity-40 animate-float-slow"></div>
                
                <!-- Floating Square 2 -->
                <div class="absolute top-40 right-[15%] w-12 h-12 border-2 border-uco-yellow-300 rotate-45 opacity-30 animate-float-slower"></div>
                
                <!-- Floating Circle 3 -->
                <div class="absolute bottom-32 left-[20%] w-20 h-20 border-2 border-uco-orange-200 rounded-full opacity-30 animate-float-medium"></div>
                
                <!-- Floating Triangle 4 -->
                <svg class="absolute top-[60%] right-[8%] w-14 h-14 opacity-25 animate-float-slow" viewBox="0 0 100 100">
                    <polygon points="50,10 90,90 10,90" fill="none" stroke="currentColor" stroke-width="3" class="text-uco-yellow-400"/>
                </svg>
                
                <!-- Floating Squiggle 5 -->
                <svg class="absolute bottom-20 right-[25%] w-24 h-16 opacity-20 animate-float-slower" viewBox="0 0 120 80">
                    <path d="M10,40 Q30,20 50,40 T90,40" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" class="text-uco-orange-300"/>
                </svg>
                
                <!-- Floating Star 6 -->
                <svg class="absolute top-[30%] left-[5%] w-10 h-10 opacity-25 animate-float-medium" viewBox="0 0 51 48">
                    <path d="M25.5,0 L31,18 L50,18 L35,29 L40,48 L25.5,37 L11,48 L16,29 L1,18 L20,18 Z" fill="none" stroke="currentColor" stroke-width="2" class="text-uco-yellow-300"/>
                </svg>
                
                <!-- Small dots scattered -->
                <div class="absolute top-[25%] right-[30%] w-2 h-2 bg-uco-orange-300 rounded-full opacity-40 animate-pulse-slow"></div>
                <div class="absolute top-[70%] left-[35%] w-2 h-2 bg-uco-yellow-300 rounded-full opacity-40 animate-pulse-slower"></div>
                <div class="absolute bottom-[40%] right-[12%] w-2 h-2 bg-uco-orange-200 rounded-full opacity-30 animate-pulse-slow"></div>
            </div>

            {{-- Global Toast Notifications --}}
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
        </main>
    </div>
    
    <style>
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) translateX(0px); }
            50% { transform: translateY(-20px) translateX(10px); }
        }
        
        @keyframes float-slower {
            0%, 100% { transform: translateY(0px) translateX(0px) rotate(45deg); }
            50% { transform: translateY(-25px) translateX(-10px) rotate(50deg); }
        }
        
        @keyframes float-medium {
            0%, 100% { transform: translateY(0px) translateX(0px); }
            50% { transform: translateY(-15px) translateX(-15px); }
        }
        
        @keyframes pulse-slow {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50% { opacity: 0.2; transform: scale(1.2); }
        }
        
        @keyframes pulse-slower {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.15; transform: scale(1.3); }
        }
        
        .animate-float-slow {
            animation: float-slow 8s ease-in-out infinite;
        }
        
        .animate-float-slower {
            animation: float-slower 12s ease-in-out infinite;
        }
        
        .animate-float-medium {
            animation: float-medium 10s ease-in-out infinite;
        }
        
        .animate-pulse-slow {
            animation: pulse-slow 4s ease-in-out infinite;
        }
        
        .animate-pulse-slower {
            animation: pulse-slower 6s ease-in-out infinite;
        }
    </style>
</body>
</html>
