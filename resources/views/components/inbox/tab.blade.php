{{-- --- Inbox tab button; the count shows when the tab has items --- --}}
@props(['name', 'label', 'count'])

<button type="button" @click="activeTab = @js($name)" class="tab" :class="{ 'tab-active': activeTab === @js($name) }" :aria-selected="activeTab === @js($name)" role="tab">
    <span>{{ $label }}</span>
    @if ($count > 0)
        <span class="badge badge-neutral">{{ $count }}</span>
    @endif
</button>
