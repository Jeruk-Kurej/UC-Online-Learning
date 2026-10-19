{{-- --- Products or Services block on the business page: title with the add button, then the cards or an empty note --- --}}
@props(['business', 'type', 'offerings', 'canManage', 'emptyMessage'])

<x-ui.section :title="ucfirst($type) . 's'">
    @auth
        @if ($canManage)
            <x-slot:action>
                <a href="{{ route('businesses.' . $type . 's.create', $business) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i>
                    Add {{ $type }}
                </a>
            </x-slot:action>
        @endif
    @endauth

    @if ($offerings->isNotEmpty())
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($offerings as $offering)
                <x-business.offering-card :business="$business" :offering="$offering" :can-manage="$canManage" />
            @endforeach
        </div>
    @else
        <x-ui.empty-state title="Nothing here yet">{{ $emptyMessage }}</x-ui.empty-state>
    @endif
</x-ui.section>
