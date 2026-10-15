{{-- --- Products or Services block on the business page: header with the add button, then the cards or an empty note --- --}}
@props(['business', 'type', 'offerings', 'canManage', 'eyebrow', 'emptyMessage', 'groupActions' => false])

<div class="bg-white shadow-lg sm:rounded-lg border border-soft-gray-100 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-1 h-8 bg-uco-orange-500 rounded-full"></div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-uco-orange-500 mb-0.5">{{ $eyebrow }}</p>
                <h2 class="text-xl font-bold text-gray-900">Our {{ ucfirst($type) }}s</h2>
            </div>
        </div>
        @auth
            @if ($canManage)
                @if ($groupActions)
                    <div class="flex items-center gap-2">
                @endif
                <a href="{{ route('businesses.'.$type.'s.create', $business) }}"
                    class="btn-uco btn-uco-primary">
                    <i class="bi bi-plus-lg"></i>
                    Add {{ ucfirst($type) }}
                </a>
                @if ($groupActions)
                    </div>
                @endif
            @endif
        @endauth
    </div>

    <div class="p-6">
        @if ($offerings->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($offerings as $offering)
                    <x-business.offering-card :business="$business" :offering="$offering" :can-manage="$canManage" />
                @endforeach
            </div>
        @else
            <div class="flex items-center justify-center py-6 px-4 bg-gray-50/50 rounded-lg border border-dashed border-gray-200">
                <span class="text-sm text-gray-500">{{ $emptyMessage }}</span>
            </div>
        @endif
    </div>
</div>
