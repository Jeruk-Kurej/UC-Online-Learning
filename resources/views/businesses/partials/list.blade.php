<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 transition-opacity duration-300">
    @forelse ($businesses as $business)
        <div class="bg-white border border-gray-100 rounded-lg overflow-hidden hover:border-gray-200 hover:shadow-lg transition-all duration-300 group relative flex flex-col h-full reveal-on-scroll" style="transition-delay: {{ ($loop->index % 6) * 50 }}ms">
            <a href="{{ $viewType === 'entrepreneur' ? route('businesses.show', $business) : route('intrapreneurs.show', $business) }}" class="block p-6 flex flex-col justify-between h-full">
                <div class="flex items-start gap-5">
                    <div class="w-20 h-20 bg-gray-50 rounded-xl flex items-center justify-center border border-gray-100 group-hover:border-gray-200 transition-colors overflow-hidden flex-shrink-0 shadow-sm">
                        @if($business->logo_url)
                            <img src="{{ $business->logo_url }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-300 select-none">
                                <span class="text-2xl font-black opacity-30 select-none">{{ substr($business->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col gap-1 mb-2">
                            <div class="flex items-center gap-2">
                            </div>
                            <h3 class="font-extrabold text-gray-900 text-lg md:text-xl leading-tight group-hover:text-gray-700 transition-colors line-clamp-2">
                                {{ $business->name }}
                            </h3>
                            @if($business->category)
                                <span class="text-[10px] md:text-xs font-bold uppercase tracking-wider text-gray-500 mt-0.5">
                                    {{ $business->category->name }}
                                </span>
                            @endif
                        </div>
                        @if($viewType === 'entrepreneur' && $business->city)
                            <div class="flex items-center gap-1.5 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-wide">
                                <i class="bi bi-geo-alt-fill text-orange-500"></i>
                                {{ $business->city }}
                            </div>
                        @endif
                        @if($viewType === 'intrapreneur' && $business->position)
                            <div class="flex items-center gap-1.5 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-wide truncate" title="{{ $business->position }}">
                                <i class="bi bi-person-badge-fill text-orange-500 flex-shrink-0"></i>
                                {{ $business->position }}
                            </div>
                        @endif
                    </div>
                </div>

                @if($business->unique_value_proposition || $business->description)
                    <p class="text-xs text-gray-500 line-clamp-3 mb-4 leading-relaxed font-normal mt-4">
                        {{ $business->unique_value_proposition ?? $business->description }}
                    </p>
                @endif

                <div class="flex items-end justify-between gap-4 pt-4 border-t border-gray-100 mt-auto">
                    <div class="space-y-3 min-w-0 flex-1">


                        @if($business->user)
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl overflow-hidden border border-gray-100 bg-gray-50 flex items-center justify-center flex-shrink-0 shadow-sm">
                                    @if($business->user->profile_photo_url)
                                        <img src="{{ $business->user->profile_photo_url }}" alt="{{ $business->user->name }}" class="w-full h-full object-contain bg-slate-50">
                                    @else
                                        <span class="text-xs font-black text-gray-300">{{ strtoupper(substr($business->user->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-gray-800">
                                        {{ $business->user->name }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <i class="bi bi-arrow-right-circle text-gray-300 group-hover:text-gray-900 text-2xl transition-all group-hover:translate-x-1"></i>
                </div>
            </a>
        </div>
    @empty
        <div class="col-span-full text-center py-12 text-gray-500">No businesses found.</div>
    @endforelse
</div>

<div class="mt-8 pagination-ajax">
    {{ $businesses->links() }}
</div>

<style>
    /* Force page numbers to show on mobile viewports for Laravel Tailwind Pagination */
    .pagination-ajax nav > div:first-child {
        display: none !important; /* Hide the simple 'Previous/Next' mobile fallback */
    }
    .pagination-ajax nav > div:last-child {
        display: flex !important; /* Force the desktop version containing page numbers to show */
        flex-direction: column !important;
        align-items: center !important;
        gap: 12px !important;
    }
    @media (min-width: 640px) {
        .pagination-ajax nav > div:last-child {
            flex-direction: row !important;
            justify-content: space-between !important;
        }
    }
    /* Force all page numbers inside the inline-flex block to show on mobile */
    .pagination-ajax nav > div:last-child div:last-child > span {
        display: inline-flex !important;
    }
    .pagination-ajax nav > div:last-child div:last-child > span > * {
        display: inline-flex !important;
    }
</style>
