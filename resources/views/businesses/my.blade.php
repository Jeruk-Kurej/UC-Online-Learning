<x-app-layout>
    <div class="w-full max-w-[1600px] 2xl:max-w-[1720px] mx-auto py-8">
        {{-- Page Header --}}
        <div class="mb-12 reveal-on-scroll">
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight mb-2">My Business Portfolio</h1>
            <p class="text-lg text-gray-500 max-w-2xl leading-relaxed mb-8">Manage and grow your entrepreneurial ventures within the UCO community.</p>
            
            @if($myBusinesses->count() > 0)
                <a href="{{ route('businesses.create') }}" class="inline-flex items-center justify-center px-6 py-3.5 bg-uco-orange-500 text-white font-bold rounded-xl hover:bg-uco-orange-600 hover:-translate-y-1 shadow-lg shadow-uco-orange-200 transition-all duration-300 group">
                    <i class="bi bi-plus-circle-fill mr-2.5 text-lg group-hover:rotate-90 transition-transform duration-300"></i>
                    Register New Business
                </a>
            @endif
        </div>

        {{-- Businesses Grid --}}
        @if($myBusinesses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                @foreach($myBusinesses as $b)
                    @php $delay = ($loop->index % 12) * 50; @endphp
                    <a href="{{ route('businesses.show', $b) }}" 
                       class="group bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-2xl hover:border-uco-orange-300 transition-all duration-500 overflow-hidden flex flex-col reveal-on-scroll" 
                       style="transition-delay: {{ $delay }}ms;">
                        {{-- Cover Image / Logo --}}
                        <div class="h-48 bg-gray-50 relative overflow-hidden">
                            @php
                                // Ensure photos is a collection before calling collection methods to avoid null errors
                                $photos = collect($b->photos ?? []);
                                $cover = optional($photos->where('is_primary', true)->first())->photo_path ?? optional($photos->first())->photo_path ?? null;
                                $coverUrl = $cover ? storage_image_url($cover, 'preview') : null;
                            @endphp
                            @if($coverUrl)
                                <img src="{{ $coverUrl }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-200">
                                    <i class="bi bi-shop text-5xl text-gray-300"></i>
                                </div>
                            @endif
                            
                            {{-- Logo Overlay --}}
                            <div class="absolute bottom-4 left-4">
                                @php $logo = $b->logo_url ? storage_image_url($b->logo_url, 'logo_thumb') : null; @endphp
                                @if($logo)
                                    <img src="{{ $logo }}" class="w-16 h-16 rounded-xl bg-white p-1.5 shadow-lg border border-white">
                                @endif
                            </div>

                            {{-- Status Badge (Dynamic based on status) --}}
                            <div class="absolute top-4 left-4 flex gap-2">
                                <div class="px-2.5 py-1 rounded-lg shadow-sm flex items-center gap-1.5 text-[10px] font-black uppercase
                                    {{ $b->status === 'approved' ? 'bg-green-500 text-white' : ($b->status === 'rejected' ? 'bg-red-500 text-white' : 'bg-uco-orange-500 text-white') }}">
                                    <i class="bi {{ $b->status === 'approved' ? 'bi-check-circle-fill' : ($b->status === 'rejected' ? 'bi-x-circle-fill' : 'bi-hourglass-split') }}"></i>
                                    {{ $b->status_label }}
                                </div>
                            </div>

                            {{-- Featured Badge --}}
                            @if($b->is_featured && $b->status === 'approved')
                                <div class="absolute top-4 right-4 bg-yellow-400 text-yellow-900 text-[10px] font-black uppercase px-2.5 py-1 rounded-lg shadow-sm flex items-center gap-1.5">
                                    <i class="bi bi-star-fill"></i> Featured
                                </div>
                            @endif
                        </div>

                        <div class="p-6 flex-1 flex flex-col">
                            <div class="mb-4 text-left">
                                <span class="inline-block px-2.5 py-1 rounded-lg bg-soft-gray-100 text-soft-gray-600 text-[10px] font-bold uppercase tracking-wider mb-2">
                                    {{ optional(optional($b->category))->name ?? 'Uncategorized' }}
                                </span>
                                <h3 class="text-xl font-bold text-gray-900 group-hover:text-uco-orange-600 transition-colors line-clamp-1">{{ $b->name }}</h3>
                            </div>
                            
                            <p class="text-sm text-gray-500 line-clamp-2 mb-4 flex-1 italic text-left">
                                {{ $b->description ?: 'No description provided' }}
                            </p>

                            @if(in_array($b->status, ['rejected', 'need_revision']) && $b->rejection_reason)
                                <div class="mb-4 p-3 {{ $b->status === 'rejected' ? 'bg-red-50 border-red-100' : 'bg-blue-50 border-blue-100' }} border rounded-xl">
                                    <p class="text-[10px] font-bold {{ $b->status === 'rejected' ? 'text-red-600' : 'text-blue-600' }} uppercase tracking-wider mb-1">
                                        {{ $b->status === 'rejected' ? 'Rejection Reason:' : 'Revision Feedback:' }}
                                    </p>
                                    <p class="text-xs {{ $b->status === 'rejected' ? 'text-red-700' : 'text-blue-700' }} italic">"{{ $b->rejection_reason }}"</p>
                                </div>
                            @endif

                            <div class="flex items-center justify-end pt-5 border-t border-gray-100 mt-auto">
                                <div class="text-uco-orange-500 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all">
                                    <i class="bi bi-arrow-right-circle-fill text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
        @else
            <div class="bg-white border-2 border-dashed border-gray-200 rounded-3xl p-20 text-center reveal-on-scroll">
                <div class="w-24 h-24 bg-soft-gray-50 text-soft-gray-300 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
                    <i class="bi bi-shop-window text-5xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">No Businesses Found</h2>
                <p class="text-gray-500 max-w-md mx-auto mb-10 leading-relaxed italic">You haven't registered any businesses yet. Start showcasing your ventures to the UCO community today!</p>
                <a href="{{ route('businesses.create') }}" class="inline-flex items-center px-8 py-4 bg-uco-orange-500 text-white font-bold rounded-xl hover:bg-uco-orange-600 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 shadow-xl shadow-uco-orange-100">
                    <i class="bi bi-plus-lg mr-2"></i>
                    Register Your First Business
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
