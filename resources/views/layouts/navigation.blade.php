<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-t-4 border-line border-t-brand-500 bg-white" aria-label="Main">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            {{-- --- Logo and name --- --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo-uco.png') }}" alt="" class="h-10 w-10 object-contain">
                <span class="font-display text-xl font-semibold text-brand-900">UC Online Learning</span>
            </a>

            {{-- --- Links, bell and account on wide screens --- --}}
            <div class="hidden items-center gap-6 md:flex">
                <x-nav.link look="bar" :href="route('home')" :active="request()->routeIs('home')">Home</x-nav.link>
                <x-nav.link look="bar" :href="route('businesses.index')" :active="request()->routeIs('businesses.index', 'businesses.show', 'intrapreneurs.show', 'showcase.resolve')">Directory</x-nav.link>
                <x-nav.link look="bar" :href="route('about')" :active="request()->routeIs('about')">About</x-nav.link>

                @auth
                    <a href="{{ route('inbox.index') }}" class="relative rounded-full p-2 text-gray-700 hover:bg-gray-100 hover:text-brand-900" aria-label="Inbox{{ $unreadInboxCount > 0 ? ', ' . $unreadInboxCount . ' unread' : '' }}">
                        <i class="bi bi-bell text-xl" aria-hidden="true"></i>
                        @if ($unreadInboxCount > 0)
                            <span class="absolute -right-0.5 -top-0.5 flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-red-700 px-1 text-xs font-semibold text-white">{{ $unreadInboxCount }}</span>
                        @endif
                    </a>

                    {{-- --- Account menu --- --}}
                    <div class="relative" x-data="{ open: false }" @click.away="open = false" @keydown.escape.window="open = false">
                        <button type="button" @click="open = !open" class="flex items-center gap-2 rounded-full px-2 py-1.5 text-base text-gray-800 hover:bg-gray-100" :aria-expanded="open" aria-haspopup="true">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-900">{{ $authUser->initial }}</span>
                            <span class="max-w-[10rem] truncate">{{ $authUser->name }}</span>
                            <i class="bi bi-chevron-down text-xs" aria-hidden="true"></i>
                        </button>

                        <div x-show="open" x-cloak class="absolute right-0 z-50 mt-2 w-64 overflow-hidden rounded-lg border border-line bg-white py-1 shadow-lg">
                            <div class="border-b border-line px-4 py-3">
                                <p class="text-sm text-gray-600">Signed in as</p>
                                <p class="truncate text-base font-medium text-gray-900">{{ $authUser->email }}</p>
                            </div>

                            @if ($authUser->isAdmin())
                                <x-nav.link look="dropdown" :href="route('users.index')">Manage users</x-nav.link>
                                <x-nav.link look="dropdown" :href="route('businesses.admin')">Manage businesses</x-nav.link>
                                <x-nav.link look="dropdown" :href="route('uc-testimonies.admin')">Manage testimonies</x-nav.link>
                                <x-nav.link look="dropdown" :href="route('pages.edit', 'about')">Edit About page</x-nav.link>
                                <x-nav.link look="dropdown" :href="route('profile.edit')">Change password</x-nav.link>
                            @else
                                <x-nav.link look="dropdown" :href="route('profile.edit')">My profile</x-nav.link>
                                <x-nav.link look="dropdown" :href="route('businesses.my')">My businesses</x-nav.link>
                                <x-nav.link look="dropdown" :href="route('uc-testimonies.my')">My testimony</x-nav.link>
                                <x-nav.link look="dropdown" :href="route('inbox.index')">Inbox</x-nav.link>
                            @endif

                            <form method="POST" action="{{ route('logout') }}" class="border-t border-line">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2.5 text-left text-base text-red-700 hover:bg-red-50">Log out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Log in</a>
                @endauth
            </div>

            {{-- --- Menu button on phones --- --}}
            <button type="button" @click="open = !open" class="flex items-center gap-2 rounded-full px-3 py-2 text-base font-medium text-gray-800 hover:bg-gray-100 md:hidden" :aria-expanded="open" aria-controls="mobile-menu">
                <i class="bi" :class="open ? 'bi-x-lg' : 'bi-list'" aria-hidden="true"></i>
                <span>Menu</span>
            </button>
        </div>
    </div>

    {{-- --- Menu on phones --- --}}
    <div id="mobile-menu" x-show="open" x-cloak class="border-t border-line bg-white md:hidden">
        <div class="mx-auto max-w-6xl space-y-1 px-4 py-3">
            <x-nav.link look="menu" :href="route('home')" :active="request()->routeIs('home')">Home</x-nav.link>
            <x-nav.link look="menu" :href="route('businesses.index')" :active="request()->routeIs('businesses.index', 'businesses.show', 'intrapreneurs.show', 'showcase.resolve')">Directory</x-nav.link>
            <x-nav.link look="menu" :href="route('about')" :active="request()->routeIs('about')">About</x-nav.link>

            @auth
                <div class="mt-3 border-t border-line pt-3">
                    <p class="px-3 pb-1 text-sm text-gray-600">Signed in as {{ $authUser->name }}</p>

                    @if ($authUser->isAdmin())
                        <x-nav.link look="account" :href="route('users.index')" :active="request()->routeIs('users.*')">Manage users</x-nav.link>
                        <x-nav.link look="account" :href="route('businesses.admin')" :active="request()->routeIs('businesses.admin')">Manage businesses</x-nav.link>
                        <x-nav.link look="account" :href="route('uc-testimonies.admin')" :active="request()->routeIs('uc-testimonies.admin')">Manage testimonies</x-nav.link>
                        <x-nav.link look="account" :href="route('pages.edit', 'about')" :active="request()->routeIs('pages.edit')">Edit About page</x-nav.link>
                        <x-nav.link look="account" :href="route('profile.edit')" :active="request()->routeIs('profile.*')">Change password</x-nav.link>
                    @else
                        <x-nav.link look="account" :href="route('profile.edit')" :active="request()->routeIs('profile.*')">My profile</x-nav.link>
                        <x-nav.link look="account" :href="route('businesses.my')" :active="request()->routeIs('businesses.my')">My businesses</x-nav.link>
                        <x-nav.link look="account" :href="route('uc-testimonies.my')" :active="request()->routeIs('uc-testimonies.my')">My testimony</x-nav.link>
                        <x-nav.link look="account" :href="route('inbox.index')" :active="request()->routeIs('inbox.*')">Inbox{{ $unreadInboxCount > 0 ? ' (' . $unreadInboxCount . ' new)' : '' }}</x-nav.link>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full rounded-md px-3 py-3 text-left text-base text-red-700 hover:bg-red-50">Log out</button>
                    </form>
                </div>
            @else
                <div class="mt-3 border-t border-line pt-3">
                    <a href="{{ route('login') }}" class="btn btn-primary w-full">Log in</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
