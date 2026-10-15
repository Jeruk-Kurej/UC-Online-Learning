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
                <x-nav.link look="bar" :href="route('featured')" :active="request()->routeIs('featured')">Featured</x-nav.link>
                <x-nav.link look="bar" :href="route('businesses.index')" :active="request()->routeIs('businesses.index', 'businesses.show', 'intrapreneurs.show', 'showcase.resolve')">Business</x-nav.link>
                <x-nav.link look="bar" :href="route('about')" :active="request()->routeIs('about')">About</x-nav.link>

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
                            @if ($authUser->isAdmin())
                                <img src="{{ asset('images/logo-uco.png') }}" alt="UCO Logo" class="h-7 w-7 rounded-md object-contain">
                            @elseif($authUser->has_profile_photo)
                                <img src="{{ $authUser->profile_photo_url }}" alt="{{ $authUser->name }}" class="h-7 w-7 rounded-sm object-contain">
                            @else
                                <div class="flex h-7 w-7 items-center justify-center rounded-sm bg-uco-orange-500 text-xs font-bold text-white">{{ substr($authUser->name, 0, 1) }}</div>
                            @endif
                            {{ $authUser->name }}
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" class="absolute right-0 z-50 mt-2 w-56 rounded-xl border border-soft-gray-100 bg-white py-2 shadow-xl">
                            <div class="border-b border-gray-50 px-4 py-2">
                                <p class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ $authUser->role }}</p>
                                <p class="truncate text-sm font-bold text-gray-900">{{ $authUser->email }}</p>
                            </div>

                            @if ($authUser->isAdmin())
                                <x-nav.link look="dropdown" :href="route('users.index')">Manage Users</x-nav.link>
                                <x-nav.link look="dropdown" :href="route('businesses.admin')">Manage Businesses</x-nav.link>
                                <x-nav.link look="dropdown" :href="route('uc-testimonies.admin')">Manage Testimonies</x-nav.link>
                                <x-nav.link look="dropdown" :href="route('pages.edit', 'about')">Manage About Page (CMS)</x-nav.link>
                            @else
                                <x-nav.link look="dropdown" :href="route('profile.edit')">My Profile</x-nav.link>
                                <x-nav.link look="dropdown" :href="route('inbox.index')">My Inbox</x-nav.link>
                                <x-nav.link look="dropdown" :href="route('uc-testimonies.my')">My Testimony</x-nav.link>
                                <x-nav.link look="dropdown" :href="route('businesses.my')">My Business</x-nav.link>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">Log Out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="rounded-[1rem] bg-uco-orange-500 px-5 py-2.5 text-sm font-bold tracking-wide text-white shadow-[0_4px_10px_-2px_rgba(247,147,30,0.4)] transition-all duration-300 hover:-translate-y-0.5 hover:bg-uco-orange-600 hover:shadow-[0_8px_15px_-3px_rgba(247,147,30,0.5)]">Log in</a>
                @endauth
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
            <x-nav.link look="menu" :href="route('featured')" :active="request()->routeIs('featured')">Featured</x-nav.link>
            <x-nav.link look="menu" :href="route('businesses.index')" :active="request()->routeIs('businesses.*')">Business</x-nav.link>
            <x-nav.link look="menu" :href="route('about')" :active="request()->routeIs('about')">About UCO</x-nav.link>
            @auth
                <div class="mt-4 border-t border-gray-100 pt-4">
                    <div class="mb-3 flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-uco-orange-500 text-xs font-bold text-white">
                            {{ substr($authUser->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-widest text-gray-400">{{ $authUser->role }}</p>
                            <p class="truncate text-sm font-bold text-gray-900">{{ $authUser->name }}</p>
                        </div>
                    </div>

                    @if ($authUser->isAdmin())
                        <x-nav.link look="account" :href="route('users.index')" :active="request()->routeIs('users.*')">Manage Users</x-nav.link>
                        <x-nav.link look="account" :href="route('businesses.admin')" :active="request()->routeIs('businesses.admin')">Manage Businesses</x-nav.link>
                        <x-nav.link look="account" :href="route('uc-testimonies.admin')" :active="request()->routeIs('uc-testimonies.admin')">Manage Testimonies</x-nav.link>
                        <x-nav.link look="account" :href="route('pages.edit', 'about')" :active="request()->routeIs('pages.edit')">Manage About Page (CMS)</x-nav.link>
                    @else
                        <x-nav.link look="account" :href="route('inbox.index')" :active="request()->routeIs('inbox.*')">My Inbox & Network</x-nav.link>
                        <x-nav.link look="account" :href="route('profile.edit')" :active="request()->routeIs('profile.*')">My Profile</x-nav.link>
                        <x-nav.link look="account" :href="route('businesses.my')" :active="request()->routeIs('businesses.my')">My Business</x-nav.link>
                        <x-nav.link look="account" :href="route('uc-testimonies.my')" :active="request()->routeIs('uc-testimonies.my')">My Testimony</x-nav.link>
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
