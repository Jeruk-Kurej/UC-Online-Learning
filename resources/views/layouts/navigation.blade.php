<nav x-data="{ open: false }" class="bg-white border-b border-soft-gray-100 shadow-sm sticky top-0 z-50">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo & Brand --}}
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/Logo UCO.png') }}" alt="UCO Logo" class="w-9 h-9 object-contain">
                    <span class="text-lg font-bold text-soft-gray-900">UC Online Learning</span>
                </a>
            </div>

            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('featured') }}" class="text-sm font-bold {{ request()->routeIs('featured') ? 'text-soft-gray-900 border-b-2 border-uco-orange-500' : 'text-soft-gray-600 hover:text-soft-gray-900' }}">Featured</a>
                <a href="{{ route('businesses.index') }}" class="text-sm font-bold {{ request()->routeIs('businesses.*') ? 'text-soft-gray-900 border-b-2 border-uco-orange-500' : 'text-soft-gray-600 hover:text-soft-gray-900' }}">Businesses</a>
                <a href="{{ route('uc-testimonies.index') }}" class="text-sm font-bold {{ request()->routeIs('uc-testimonies.index') ? 'text-soft-gray-900 border-b-2 border-uco-orange-500' : 'text-soft-gray-600 hover:text-soft-gray-900' }}">Testimonies</a>
                <a href="{{ route('about') }}" class="text-sm font-bold {{ request()->routeIs('about') ? 'text-soft-gray-900 border-b-2 border-uco-orange-500' : 'text-soft-gray-600 hover:text-soft-gray-900' }}">About</a>
                
                @auth
                    {{-- Profile Dropdown --}}
                    <div class="relative group" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="text-sm font-medium text-soft-gray-700 hover:text-soft-gray-900 transition flex items-center gap-2 px-3 py-2 rounded-md hover:bg-soft-gray-50">
                            <div class="w-7 h-7 bg-uco-orange-500 rounded-md flex items-center justify-center text-white text-xs font-bold">{{ substr(auth()->user()->name, 0, 1) }}</div>
                            {{ auth()->user()->name }}
                            <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-soft-gray-100 z-50 py-2">
                            <div class="px-4 py-2 border-b border-gray-50">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ auth()->user()->role }}</p>
                                <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            
                            @if(auth()->user()->isAdmin())
                            <div class="px-4 py-2 border-b border-gray-50 bg-gray-50">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Admin Tools</p>
                                <a href="{{ route('users.index') }}" class="block py-1 text-sm text-gray-700 hover:text-uco-orange-500 font-medium">Manage Users</a>
                                <a href="{{ route('businesses.admin') }}" class="block py-1 text-sm text-gray-700 hover:text-uco-orange-500 font-medium">Manage Businesses</a>
                                <a href="{{ route('uc-testimonies.index') }}" class="block py-1 text-sm text-gray-700 hover:text-uco-orange-500 font-medium">Manage Testimonies</a>
                            </div>
                            @endif

                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">My Profile</a>
                            @if(!auth()->user()->isAdmin())
                                <a href="{{ route('uc-testimonies.my') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">My Testimony</a>
                                <a href="{{ route('businesses.my') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">My Businesses</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Log Out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-black transition shadow-sm">Log in</a>
                @endif
            </div>

            {{-- Mobile Menu Button --}}
            <div class="flex md:hidden">
                <button @click="open = ! open" class="p-2 rounded-lg text-soft-gray-700">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden border-t bg-white">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('featured') }}" class="block py-3 text-base font-bold text-gray-900">Home</a>
            <a href="{{ route('businesses.index') }}" class="block py-3 text-base font-bold text-gray-900">Showcase</a>
            <a href="{{ route('uc-testimonies.index') }}" class="block py-3 text-base font-bold text-gray-900">Testimonies</a>
            @auth
                <div class="pt-4 border-t mt-4">
                    <p class="text-xs font-bold text-gray-400 uppercase mb-2">{{ auth()->user()->name }}</p>
                    
                    @if(auth()->user()->isAdmin())
                        <div class="mb-3 bg-gray-50 rounded-lg p-3">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Admin Tools</p>
                            <a href="{{ route('users.index') }}" class="block py-1 text-sm font-bold {{ request()->routeIs('users.*') ? 'text-uco-orange-500' : 'text-gray-700' }}">Manage Users</a>
                            <a href="{{ route('businesses.admin') }}" class="block py-1 text-sm font-bold {{ request()->routeIs('businesses.admin') ? 'text-uco-orange-500' : 'text-gray-700' }}">Manage Businesses</a>
                        </div>
                    @endif

                    <a href="{{ route('profile.edit') }}" class="block py-2 text-sm text-gray-600">My Profile</a>
                    @if(!auth()->user()->isAdmin())
                        <a href="{{ route('businesses.my') }}" class="block py-2 text-sm text-gray-600">My Businesses</a>
                        <a href="{{ route('uc-testimonies.my') }}" class="block py-2 text-sm text-gray-600">My Testimony</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block py-2 text-sm text-red-600">Log Out</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="block py-3 text-base font-bold text-uco-orange-500">Log in</a>
            @endauth
        </div>
    </div>
</nav>