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

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/pages/welcome.css'])

</head>

<body class="overflow-x-hidden bg-soft-white antialiased">
    <div class="relative flex min-h-screen flex-col overflow-x-hidden">
        {{-- --- Hero Content --- --}}
        <main class="relative z-10 flex flex-shrink-0 items-center overflow-hidden py-20">
            <div class="relative z-10 mx-auto w-full max-w-6xl px-8">
                <div class="grid items-center gap-16 lg:grid-cols-2">
                    {{-- --- Left: Content --- --}}
                    <div class="reveal-on-scroll relative space-y-6">
                        <div class="inline-flex items-center gap-2 rounded-full border border-soft-gray-200 bg-white px-4 py-2 shadow-sm">
                            <div class="h-2 w-2 rounded-full bg-gradient-to-r from-uco-orange-400 to-uco-yellow-400"></div>
                            <span class="text-xs font-semibold text-soft-gray-700">For UC Students & Alumni</span>
                        </div>

                        <h1 class="text-5xl font-bold leading-tight tracking-tight text-soft-gray-900">
                            Business Portfolio<br>
                            for <span class="text-soft-gray-900">UC Online Learning Students</span>
                        </h1>

                        <p class="max-w-lg text-lg leading-relaxed text-soft-gray-600">
                            Connect with Universitas Ciputra's professional community. Discover businesses, explore achievements, showcase products, and grow your network.
                        </p>

                        {{-- --- Feature Pills --- --}}
                        <div class="flex flex-wrap gap-3 pt-2">
                            <div class="rounded-lg border border-soft-gray-200 bg-white px-4 py-2 text-sm font-medium text-soft-gray-700 shadow-sm transition-shadow hover:shadow-md">
                                🏢 Business Profiles
                            </div>
                            <div class="rounded-lg border border-soft-gray-200 bg-white px-4 py-2 text-sm font-medium text-soft-gray-700 shadow-sm transition-shadow hover:shadow-md">
                                📦 Product Catalog
                            </div>
                            <div class="rounded-lg border border-soft-gray-200 bg-white px-4 py-2 text-sm font-medium text-soft-gray-700 shadow-sm transition-shadow hover:shadow-md">
                                🤝 Alumni Network
                            </div>
                        </div>
                    </div>

                    {{-- --- Right: Login Form --- --}}
                    <div class="reveal-on-scroll relative overflow-hidden rounded-[2.5rem] border border-white/60 bg-white/90 p-10 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.08)] backdrop-blur-2xl delay-200">
                        {{-- --- Decorative corner elements --- --}}
                        <div class="absolute right-0 top-0 h-40 w-40 rounded-bl-[4rem] bg-gradient-to-br from-uco-orange-100 to-uco-yellow-100 opacity-50"></div>
                        <div class="absolute bottom-0 left-0 h-32 w-32 rounded-tr-[4rem] bg-gradient-to-tr from-uco-yellow-100 to-uco-orange-100 opacity-40"></div>

                        <div class="relative z-10 mb-8">
                            <h2 class="mb-1.5 text-3xl font-black tracking-tight text-gray-900">Sign in</h2>
                            <p class="text-sm font-medium text-gray-500">Access your business hub</p>
                        </div>

                        <form method="POST" action="/login" class="relative z-10 space-y-5">
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
                                    class="w-full rounded-[7px] bg-uco-orange-500 py-3 text-sm font-bold uppercase tracking-wide text-white shadow-sm transition-all duration-200 hover:bg-uco-orange-600">
                                Sign in to Dashboard
                            </button>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-soft-gray-200"></div>
                                </div>
                                <div class="relative flex justify-center text-xs">
                                    <span class="bg-white/90 px-2 font-medium text-gray-400">or continue with</span>
                                </div>
                            </div>

                            <a href="{{ route('auth.google.redirect') }}"
                               class="flex w-full items-center justify-center gap-3 rounded-[7px] border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-700 shadow-sm transition-all duration-200 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-900">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                                </svg>
                                <span>Sign in with Google</span>
                            </a>
                        </form>

                        {{-- --- Back to Home Button --- --}}
                        <div class="relative z-10 mt-6 border-t border-gray-100 pt-6">
                            @if (auth()->check())
                                <a href="{{ route('businesses.index') }}"
                                   class="block w-full rounded-[7px] border border-gray-200 bg-gray-50 py-3 text-center text-sm font-semibold text-gray-600 transition-all duration-200 hover:bg-gray-100 hover:text-gray-900">
                                    Browse Businesses
                                </a>
                            @else
                                <a href="{{ route('featured') }}"
                                   class="block w-full rounded-[7px] border border-gray-200 bg-gray-50 py-3 text-center text-sm font-semibold text-gray-600 transition-all duration-200 hover:bg-gray-100 hover:text-gray-900">
                                    Browse as Guest
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- --- Floating Decorative Elements --- --}}
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div class="animate-float-slow absolute left-[10%] top-20 h-16 w-16 rounded-full border-2 border-uco-orange-300 opacity-40"></div>
                <div class="animate-float-slower absolute right-[15%] top-40 h-12 w-12 rotate-45 border-2 border-uco-yellow-300 opacity-30"></div>
                <div class="animate-float-medium absolute bottom-32 left-[20%] h-20 w-20 rounded-full border-2 border-uco-orange-200 opacity-30"></div>
                <svg class="animate-float-slow absolute right-[8%] top-[60%] h-14 w-14 opacity-25" viewBox="0 0 100 100">
                    <polygon points="50,10 90,90 10,90" fill="none" stroke="currentColor" stroke-width="3" class="text-uco-yellow-400" />
                </svg>
                <svg class="animate-float-slower absolute bottom-20 right-[25%] h-16 w-24 opacity-20" viewBox="0 0 120 80">
                    <path d="M10,40 Q30,20 50,40 T90,40" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" class="text-uco-orange-300" />
                </svg>
                <svg class="animate-float-medium absolute left-[5%] top-[30%] h-10 w-10 opacity-25" viewBox="0 0 51 48">
                    <path d="M25.5,0 L31,18 L50,18 L35,29 L40,48 L25.5,37 L11,48 L16,29 L1,18 L20,18 Z" fill="none" stroke="currentColor" stroke-width="2" class="text-uco-yellow-300" />
                </svg>
                <div class="animate-pulse-slow absolute right-[30%] top-[25%] h-2 w-2 rounded-full bg-uco-orange-300 opacity-40"></div>
                <div class="animate-pulse-slower absolute left-[35%] top-[70%] h-2 w-2 rounded-full bg-uco-yellow-300 opacity-40"></div>
                <div class="animate-pulse-slow absolute bottom-[40%] right-[12%] h-2 w-2 rounded-full bg-uco-orange-200 opacity-30"></div>
            </div>
        </main>

        {{-- --- Portfolio Overview Section --- --}}
        <section class="relative z-20 border-t border-soft-gray-100 bg-white py-24">
            <div class="mx-auto max-w-6xl px-8">
                <div class="relative overflow-hidden rounded-[2.5rem] border border-soft-gray-100 bg-gradient-to-br from-soft-gray-50 to-white p-8 shadow-sm md:p-12">
                    <div class="pointer-events-none absolute right-0 top-0 p-8 opacity-[0.03]">
                        <i class="bi bi-grid-3x3-gap-fill text-[120px]"></i>
                    </div>

                    <div class="max-w-3xl">
                        <h2 class="mb-4 text-xs font-black uppercase tracking-[0.2em] text-uco-orange-500">Project Overview</h2>
                        <h3 class="mb-6 text-3xl font-bold leading-tight text-soft-gray-900">
                            Empowering the next generation of <span class="text-uco-orange-600">Entrepreneurial Leaders</span>
                        </h3>
                        <p class="mb-8 text-base leading-relaxed text-soft-gray-600">
                            This web portfolio serves as a central hub for Universitas Ciputra Online students and alumni to showcase their professional growth. Whether you are an <strong>Entrepreneur</strong> building a new venture or an <strong>Intrapreneur</strong> driving innovation within an organization, this platform highlights your unique journey, products, and achievements to the global community.
                        </p>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 text-uco-orange-600">
                                    <i class="bi bi-check2-circle text-lg"></i>
                                </div>
                                <span class="text-sm font-bold text-soft-gray-800">Verified Profiles</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 text-uco-orange-600">
                                    <i class="bi bi-display text-lg"></i>
                                </div>
                                <span class="text-sm font-bold text-soft-gray-800">Business Catalog</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 text-uco-orange-600">
                                    <i class="bi bi-award text-lg"></i>
                                </div>
                                <span class="text-sm font-bold text-soft-gray-800">AI-Verified Merit</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- --- Dynamic JS Toast Trigger --- --}}
        <x-flash.guest-toasts />
    </div>

</body>

</html>
