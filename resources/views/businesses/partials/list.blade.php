<div class="reveal-stagger-6 grid grid-cols-1 gap-8 transition-opacity duration-300 md:grid-cols-2 lg:grid-cols-3">
    @forelse ($businesses as $business)
        <div class="reveal-on-scroll group relative flex h-full flex-col overflow-hidden rounded-lg border border-gray-100 bg-white transition-all duration-300 hover:border-gray-200 hover:shadow-lg">
            <a href="{{ $viewType === 'entrepreneur' ? route('businesses.show', $business) : route('intrapreneurs.show', $business) }}" class="block flex h-full flex-col justify-between p-6">
                <div class="flex items-start gap-5">
                    <div class="flex h-20 w-20 flex-shrink-0 items-center justify-center overflow-hidden rounded-sm">
                        @if ($business->logo_url)
                            <img src="{{ $business->logo_url }}" class="h-full w-full object-contain transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="flex h-full w-full select-none items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 text-gray-600">
                                <span class="select-none text-2xl font-bold opacity-30">{{ substr($business->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="mb-2 flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                            </div>
                            <h3 class="line-clamp-2 text-lg font-bold leading-tight text-gray-900 transition-colors group-hover:text-gray-700 md:text-xl">
                                {{ $business->name }}
                            </h3>
                            @if ($business->category)
                                <span class="mt-0.5 text-xs font-bold text-gray-600 md:text-xs">
                                    {{ $business->category->name }}
                                </span>
                            @endif
                        </div>
                        @if ($viewType === 'entrepreneur' && $business->city)
                            <div class="mb-2 flex items-center gap-1.5 text-xs font-bold text-gray-600">
                                <i class="bi bi-geo-alt-fill text-orange-500"></i>
                                {{ $business->city }}
                            </div>
                        @endif
                        @if ($viewType === 'intrapreneur' && $business->position)
                            <div class="mb-2 flex items-center gap-1.5 truncate text-xs font-bold text-gray-600" title="{{ $business->position }}">
                                <i class="bi bi-person-badge-fill flex-shrink-0 text-orange-500"></i>
                                {{ $business->position }}
                            </div>
                        @endif
                    </div>
                </div>

                @if ($business->unique_value_proposition || $business->description)
                    <p class="mb-4 mt-4 line-clamp-3 text-xs font-normal leading-relaxed text-gray-600">
                        {{ $business->unique_value_proposition ?? $business->description }}
                    </p>
                @endif

                <div class="mt-auto flex items-end justify-between gap-4 border-t border-gray-100 pt-4">
                    <div class="min-w-0 flex-1 space-y-3">

                        @if ($business->public_owner)
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center overflow-hidden rounded-sm">
                                    @if ($business->public_owner->profile_photo_url)
                                        <img src="{{ $business->public_owner->profile_photo_url }}" alt="{{ $business->public_owner->name }}" class="h-full w-full object-contain">
                                    @else
                                        <span class="text-xs font-bold text-gray-600">{{ $business->public_owner->initial }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-gray-800">
                                        {{ $business->public_owner->name }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <i class="bi bi-arrow-right-circle text-2xl text-gray-600 transition-all group-hover:translate-x-1 group-hover:text-gray-900"></i>
                </div>
            </a>
        </div>
    @empty
        <div class="col-span-full py-12 text-center text-gray-600">No businesses found.</div>
    @endforelse
</div>

<div class="pagination-ajax mt-8">
    {{ $businesses->links() }}
</div>
