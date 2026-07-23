<nav x-data="{ open: false }" class="bg-white border-b border-soft-gray-100 shadow-sm sticky top-0 z-50">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo & Brand --}}
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-uco.png') }}" alt="UCO Logo" class="w-9 h-9 object-contain">
                    <span class="text-lg font-bold text-soft-gray-900">UC Online Learning</span>
                </a>
            </div>

            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('featured') }}" class="text-sm font-bold {{ request()->routeIs('featured') ? 'text-soft-gray-900 border-b-2 border-uco-orange-500' : 'text-soft-gray-600 hover:text-soft-gray-900' }}">Featured</a>
                <a href="{{ route('businesses.index') }}" class="text-sm font-bold {{ (request()->routeIs('businesses.index', 'businesses.show', 'intrapreneurs.show', 'showcase.resolve')) ? 'text-soft-gray-900 border-b-2 border-uco-orange-500' : 'text-soft-gray-600 hover:text-soft-gray-900' }}">Business</a>
                {{-- <a href="{{ route('uc-testimonies.index') }}" class="text-sm font-bold {{ request()->routeIs('uc-testimonies.index') ? 'text-soft-gray-900 border-b-2 border-uco-orange-500' : 'text-soft-gray-600 hover:text-soft-gray-900' }}">Testimonies</a> --}}
                <a href="{{ route('about') }}" class="text-sm font-bold {{ request()->routeIs('about') ? 'text-soft-gray-900 border-b-2 border-uco-orange-500' : 'text-soft-gray-600 hover:text-soft-gray-900' }}">About</a>
                
                @auth
                    {{-- Inbox Notification Bell --}}
                    @php
                        $unreadInboxCount = \App\Models\Message::where('recipient_id', auth()->id())->whereNull('read_at')->count();
                    @endphp
                    <a href="{{ route('inbox.index') }}" class="relative p-2 text-soft-gray-600 hover:text-soft-gray-900 transition-colors duration-200 mr-2 flex items-center justify-center rounded-full hover:bg-soft-gray-50" title="Inbox">
                        <i class="bi bi-bell-fill text-lg"></i>
                        @if($unreadInboxCount > 0)
                            <span class="absolute top-1.5 right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-extrabold text-white ring-2 ring-white animate-pulse">
                                {{ $unreadInboxCount }}
                            </span>
                        @endif
                    </a>

                    {{-- Profile Dropdown --}}
                    <div class="relative group" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="text-sm font-medium text-soft-gray-700 hover:text-soft-gray-900 transition flex items-center gap-2 px-3 py-2 rounded-md hover:bg-soft-gray-50">
                            @if(auth()->user()->isAdmin())
                                <img src="{{ asset('images/logo-uco.png') }}" alt="UCO Logo" class="w-7 h-7 object-contain rounded-md">
                            @elseif(auth()->user()->profile_photo_url && !str_contains(auth()->user()->profile_photo_url, 'ui-avatars.com'))
                                <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="w-7 h-7 object-contain rounded-sm">
                            @else
                                <div class="w-7 h-7 bg-uco-orange-500 rounded-sm flex items-center justify-center text-white text-xs font-bold">{{ substr(auth()->user()->name, 0, 1) }}</div>
                            @endif
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
                                <a href="{{ route('users.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Manage Users</a>
                                <a href="{{ route('businesses.admin') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Manage Businesses</a>
                                <a href="{{ route('uc-testimonies.admin') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Manage Testimonies</a>
                            @endif

                            @if(!auth()->user()->isAdmin())
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">My Profile</a>
                                <a href="{{ route('inbox.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">My Inbox</a>
                                <a href="{{ route('uc-testimonies.my') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">My Testimony</a>
                                <a href="{{ route('businesses.my') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">My Business</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Log Out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 bg-uco-orange-500 text-white text-sm font-bold tracking-wide rounded-[1rem] shadow-[0_4px_10px_-2px_rgba(247,147,30,0.4)] hover:bg-uco-orange-600 hover:-translate-y-0.5 hover:shadow-[0_8px_15px_-3px_rgba(247,147,30,0.5)] transition-all duration-300">Log in</a>
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
            <a href="{{ route('businesses.index') }}" class="block py-3 text-base font-bold text-gray-900">Directory</a>
            @auth
                <div class="pt-4 border-t mt-4">
                    <p class="text-xs font-bold text-gray-400 uppercase mb-2">{{ auth()->user()->name }}</p>
                    
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('users.index') }}" class="block py-2 text-sm font-bold {{ request()->routeIs('users.*') ? 'text-uco-orange-500' : 'text-gray-700' }}">Manage Users</a>
                        <a href="{{ route('businesses.admin') }}" class="block py-2 text-sm font-bold {{ request()->routeIs('businesses.admin') ? 'text-uco-orange-500' : 'text-gray-700' }}">Manage Businesses</a>
                        <a href="{{ route('uc-testimonies.admin') }}" class="block py-2 text-sm font-bold {{ request()->routeIs('uc-testimonies.admin') ? 'text-uco-orange-500' : 'text-gray-700' }}">Manage Testimonies</a>
                    @endif

                    @if(!auth()->user()->isAdmin())
                        <a href="{{ route('profile.edit') }}" class="block py-2 text-sm text-gray-600">My Profile</a>
                        <a href="{{ route('inbox.index') }}" class="block py-2 text-sm text-gray-600">My Inbox</a>
                        <a href="{{ route('businesses.my') }}" class="block py-2 text-sm text-gray-600">My Business</a>
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