{{-- --- Content of one inbox tab; the first tab is not cloaked --- --}}
@props(['name', 'cloak' => true])

<div x-show="activeTab === @js($name)" @if ($cloak) x-cloak @endif role="tabpanel" class="w-full">
    {{ $slot }}
</div>
