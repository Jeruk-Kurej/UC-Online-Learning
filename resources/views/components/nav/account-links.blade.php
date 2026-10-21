{{-- --- The signed-in person's own pages, in the desktop dropdown or the phone menu ($look); $inboxLabel carries the count of new messages where the menu shows it --- --}}
@props(['look', 'user', 'inboxLabel' => 'Inbox'])

@if ($user->isAdmin())
    <x-nav.link :look="$look" :href="route('users.index')" :active="request()->routeIs('users.*')">Manage users</x-nav.link>
    <x-nav.link :look="$look" :href="route('businesses.admin')" :active="request()->routeIs('businesses.admin')">Manage businesses</x-nav.link>
    <x-nav.link :look="$look" :href="route('uc-testimonies.admin')" :active="request()->routeIs('uc-testimonies.admin')">Manage testimonies</x-nav.link>
    <x-nav.link :look="$look" :href="route('pages.edit', 'about')" :active="request()->routeIs('pages.edit')">Edit About page</x-nav.link>
    <x-nav.link :look="$look" :href="route('profile.edit')" :active="request()->routeIs('profile.*')">Change password</x-nav.link>
@else
    <x-nav.link :look="$look" :href="route('profile.edit')" :active="request()->routeIs('profile.*')">My profile</x-nav.link>
    <x-nav.link :look="$look" :href="route('businesses.my')" :active="request()->routeIs('businesses.my')">My businesses</x-nav.link>
    <x-nav.link :look="$look" :href="route('uc-testimonies.my')" :active="request()->routeIs('uc-testimonies.my')">My testimony</x-nav.link>
    <x-nav.link :look="$look" :href="route('inbox.index')" :active="request()->routeIs('inbox.*')">{{ $inboxLabel }}</x-nav.link>
@endif
