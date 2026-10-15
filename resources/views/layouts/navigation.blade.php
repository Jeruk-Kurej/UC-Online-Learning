<nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-soft-gray-100 bg-white shadow-sm">
    <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            {{-- --- Logo & Brand --- --}}
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-uco.png') }}" alt="UCO Logo" class="h-9 w-9 object-contain">
                    <span class="text-lg font-bold text-soft-gray-900">UC Online Learning</span>
                </a>
            </div>

            <div class="hidden items-center space-x-8 md:flex">
                <a href="{{ route('featured') }}" class="{{ request()->routeIs('featured') ? 'text-soft-gray-900 border-b-2 border-uco-orange-500' : 'text-soft-gray-600 hover:text-soft-gray-900' }} text-sm font-bold">Featured</a>
                <a href="{{ route('businesses.index') }}" class="{{ request()->routeIs('businesses.index', 'businesses.show', 'intrapreneurs.show', 'showcase.resolve') ? 'text-soft-gray-900 border-b-2 border-uco-orange-500' : 'text-soft-gray-600 hover:text-soft-gray-900' }} text-sm font-bold">Business</a>
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'text-soft-gray-900 border-b-2 border-uco-orange-500' : 'text-soft-gray-600 hover:text-soft-gray-900' }} text-sm font-bold">About</a>

                @auth
                    {{-- --- Inbox Notification Bell --- --}}
                    <a href="{{ route('inbox.index') }}" class="relative mr-2 flex items-center justify-center rounded-full p-2 text-soft-gray-600 transition-colors duration-200 hover:bg-soft-gray-50 hover:text-soft-gray-900" title="Inbox">
                        <i class="bi bi-bell-fill text-lg"></i>
                        @if ($unreadInboxCount > 0)
                            <span class="absolute right-1.5 top-1.5 flex h-4 w-4 animate-pulse items-center justify-center rounded-full bg-red-500 text-[10px] font-extrabold text-white ring-2 ring-white">
                                {{ $unreadInboxCount }}
                            </span>
                        @endif
                    </a>

                    {{-- --- Profile Dropdown --- --}}
                    <div class="group relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-soft-gray-700 transition hover:bg-soft-gray-50 hover:text-soft-gray-900">
                            @if (auth()->user()->isAdmin())
                                <img src="{{ asset('images/logo-uco.png') }}" alt="UCO Logo" class="h-7 w-7 rounded-md object-contain">
                            @elseif(auth()->user()->has_profile_photo)
                                <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="h-7 w-7 rounded-sm object-contain">
                            @else
                                <div class="flex h-7 w-7 items-center justify-center rounded-sm bg-uco-orange-500 text-xs font-bold text-white">{{ substr(auth()->user()->name, 0, 1) }}</div>
                            @endif
                            {{ auth()->user()->name }}
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" class="absolute right-0 z-50 mt-2 w-56 rounded-xl border border-soft-gray-100 bg-white py-2 shadow-xl">
                            <div class="border-b border-gray-50 px-4 py-2">
                                <p class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ auth()->user()->role }}</p>
                                <p class="truncate text-sm font-bold text-gray-900">{{ auth()->user()->email }}</p>
                            </div>

                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('users.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Manage Users</a>
                                <a href="{{ route('businesses.admin') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Manage Businesses</a>
                                <a href="{{ route('uc-testimonies.admin') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Manage Testimonies</a>
                                <a href="{{ route('pages.edit', 'about') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Manage About Page (CMS)</a>
                            @endif

                            @if (!auth()->user()->isAdmin())
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">My Profile</a>
                                <a href="{{ route('inbox.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">My Inbox</a>
                                <a href="{{ route('uc-testimonies.my') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">My Testimony</a>
                                <a href="{{ route('businesses.my') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">My Business</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">Log Out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="rounded-[1rem] bg-uco-orange-500 px-5 py-2.5 text-sm font-bold tracking-wide text-white shadow-[0_4px_10px_-2px_rgba(247,147,30,0.4)] transition-all duration-300 hover:-translate-y-0.5 hover:bg-uco-orange-600 hover:shadow-[0_8px_15px_-3px_rgba(247,147,30,0.5)]">Log in</a>
                    @endif
                </div>

                {{-- --- Mobile Menu Button --- --}}
                <div class="flex md:hidden">
                    <button @click="open = ! open" class="rounded-lg p-2 text-soft-gray-700">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- --- Mobile Menu --- --}}
        <div :class="{ 'block': open, 'hidden': !open }" class="hidden border-t border-gray-100 bg-white shadow-lg md:hidden">
            <div class="space-y-1 px-4 py-3">
                <a href="{{ route('featured') }}" class="{{ request()->routeIs('featured') ? 'text-uco-orange-500' : 'text-gray-800' }} block py-2.5 text-base font-bold">Featured</a>
                <a href="{{ route('businesses.index') }}" class="{{ request()->routeIs('businesses.*') ? 'text-uco-orange-500' : 'text-gray-800' }} block py-2.5 text-base font-bold">Business</a>
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'text-uco-orange-500' : 'text-gray-800' }} block py-2.5 text-base font-bold">About UCO</a>
                @auth
                    <div class="mt-4 border-t border-gray-100 pt-4">
                        <div class="mb-3 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-uco-orange-500 text-xs font-bold text-white">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xs font-extrabold uppercase tracking-widest text-gray-400">{{ auth()->user()->role }}</p>
                                <p class="truncate text-sm font-bold text-gray-900">{{ auth()->user()->name }}</p>
                            </div>
                        </div>

                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'text-uco-orange-500' : 'text-gray-700' }} block py-2 text-sm font-bold">Manage Users</a>
                            <a href="{{ route('businesses.admin') }}" class="{{ request()->routeIs('businesses.admin') ? 'text-uco-orange-500' : 'text-gray-700' }} block py-2 text-sm font-bold">Manage Businesses</a>
                            <a href="{{ route('uc-testimonies.admin') }}" class="{{ request()->routeIs('uc-testimonies.admin') ? 'text-uco-orange-500' : 'text-gray-700' }} block py-2 text-sm font-bold">Manage Testimonies</a>
                            <a href="{{ route('pages.edit', 'about') }}" class="{{ request()->routeIs('pages.edit') ? 'text-uco-orange-500' : 'text-gray-700' }} block py-2 text-sm font-bold">Manage About Page (CMS)</a>
                        @endif

                        @if (!auth()->user()->isAdmin())
                            <a href="{{ route('inbox.index') }}" class="{{ request()->routeIs('inbox.*') ? 'text-uco-orange-500' : 'text-gray-700' }} block py-2 text-sm font-bold">My Inbox & Network</a>
                            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'text-uco-orange-500' : 'text-gray-700' }} block py-2 text-sm font-bold">My Profile</a>
                            <a href="{{ route('businesses.my') }}" class="{{ request()->routeIs('businesses.my') ? 'text-uco-orange-500' : 'text-gray-700' }} block py-2 text-sm font-bold">My Business</a>
                            <a href="{{ route('uc-testimonies.my') }}" class="{{ request()->routeIs('uc-testimonies.my') ? 'text-uco-orange-500' : 'text-gray-700' }} block py-2 text-sm font-bold">My Testimony</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="mt-2 border-t border-gray-50 pt-2">
                            @csrf
                            <button type="submit" class="block w-full py-2 text-left text-sm font-bold text-red-600">Log Out</button>
                        </form>
                    </div>
                @else
                    <div class="mt-4 border-t border-gray-100 pt-4">
                        <a href="{{ route('login') }}" class="block w-full rounded-xl bg-uco-orange-500 py-3 text-center font-bold text-white shadow-md">Log in</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>
