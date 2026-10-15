{{-- --- Content of one inbox tab, faded in when its tab is active; the first tab is not cloaked --- --}}
@props(['name', 'cloak' => true])

<div x-show="activeTab === '{{ $name }}'" @if ($cloak) x-cloak @endif
     class="col-span-1 w-full"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform translate-y-2">
    {{ $slot }}
</div>
