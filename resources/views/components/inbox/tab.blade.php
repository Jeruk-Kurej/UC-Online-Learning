{{-- --- Inbox tab button; the count badge shows when the tab has items (orange on the main tab) --- --}}
@props(['name', 'icon', 'label', 'count', 'highlight' => false])

<button @click="activeTab = '{{ $name }}'"
        :class="activeTab === '{{ $name }}' ? 'border-uco-orange-500 text-uco-orange-600 font-extrabold border-b-2' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-semibold border-b-2'"
        class="inline-flex items-center gap-2 whitespace-nowrap px-1 py-3 text-sm transition-all">
    <i class="bi {{ $icon }} text-base"></i>
    <span>{{ $label }}</span>
    @if ($count > 0)
        <span @class([
            'ml-1.5 rounded-full px-2 py-0.5 text-xs font-bold',
            'bg-uco-orange-100 text-uco-orange-800' => $highlight,
            'bg-gray-100 text-gray-700' => !$highlight,
        ])>{{ $count }}</span>
    @endif
</button>
