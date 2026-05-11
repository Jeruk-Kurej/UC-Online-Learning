<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 transition-opacity duration-300">
    @forelse ($businesses as $business)
        <div class="bg-white border border-gray-100 rounded-lg overflow-hidden hover:border-gray-200 hover:shadow-lg transition-all duration-300 group relative flex flex-col h-full reveal-on-scroll" style="transition-delay: {{ ($loop->index % 6) * 50 }}ms">
            @auth
                @if(auth()->user()->isAdmin())
                    <div class="absolute top-3 right-3 z-10 flex flex-col gap-2">
                        {{-- Featured Toggle --}}
                        <form action="{{ route('businesses.toggle-featured', $business) }}" method="POST">
                            @csrf
                            <button type="submit"
                                title="{{ $business->is_featured ? 'Remove from featured' : 'Add to featured' }}"
                                class="w-8 h-8 rounded-full flex items-center justify-center transition-all shadow-sm border
                                    {{ $business->is_featured
                                        ? 'bg-yellow-400 border-yellow-500 text-white hover:bg-yellow-500'
                                        : 'bg-white border-gray-200 text-gray-300 hover:text-yellow-400 hover:border-yellow-300' }}">
                                <i class="bi bi-star-fill text-xs"></i>
                            </button>
                        </form>

                        {{-- Approval Button --}}
                        @if(!$business->is_visible)
                        <form action="{{ route('businesses.approve', $business) }}" method="POST">
                            @csrf
                            <button type="submit"
                                title="Approve Business"
                                class="w-8 h-8 rounded-full flex items-center justify-center transition-all shadow-sm border bg-emerald-500 border-emerald-600 text-white hover:bg-emerald-600">
                                <i class="bi bi-check-lg text-xs"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                @endif
            @endauth
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
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                    {{ $viewType === 'entrepreneur' ? 'Entrepreneur' : 'Intrapreneur' }}
                                </span>
                                @if(!$business->is_visible)
                                    <span class="text-[8px] font-black bg-red-50 text-red-600 px-1.5 py-0.5 rounded-md uppercase tracking-tighter">Pending Approval</span>
                                @endif
                            </div>
                            <h3 class="font-extrabold text-gray-900 text-xl md:text-2xl leading-tight group-hover:text-gray-700 transition-colors line-clamp-2">
                                {{ $business->name }}
                            </h3>
                            @if($business->category)
                                <span class="text-[9px] font-bold uppercase tracking-wider text-gray-500 mt-0.5">
                                    {{ $business->category->name }}
                                </span>
                            @endif
                        </div>
                        @if($business->unique_value_proposition || $business->description)
                            <p class="text-xs text-gray-500 line-clamp-3 mb-4 leading-relaxed font-normal">
                                {{ $business->unique_value_proposition ?? $business->description }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="flex items-end justify-between gap-4 pt-4 border-t border-gray-100 mt-auto">
                    <div class="space-y-3 min-w-0 flex-1">
                        @if($viewType === 'entrepreneur' && $business->city)
                            <span class="flex items-center gap-1 text-[11px] font-semibold text-gray-500">
                                <i class="bi bi-geo-alt text-gray-400"></i>
                                {{ $business->city }}
                            </span>
                        @endif

                        @if($business->user)
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl overflow-hidden border border-gray-100 bg-gray-50 flex items-center justify-center flex-shrink-0 shadow-sm">
                                    @if($business->user->profile_photo_url)
                                        <img src="{{ $business->user->profile_photo_url }}" alt="{{ $business->user->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xs font-black text-gray-300">{{ strtoupper(substr($business->user->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">UCO Student</p>
                                    <p class="truncate text-xs font-bold text-gray-800">
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
