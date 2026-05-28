<x-app-layout>
    <div class="w-full max-w-[1600px] 2xl:max-w-[1720px] mx-auto py-8 px-4 sm:px-6 lg:px-8">
        {{-- Page Header --}}
        <div class="mb-12 reveal-on-scroll">
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight mb-2">My Business & Career Portfolio</h1>
            <p class="text-lg text-gray-500 max-w-3xl leading-relaxed">
                Manage and showcase your entrepreneurial ventures and professional employment profiles within the UCO community.
            </p>
        </div>

        {{-- ── SECTION 1: MY VENTURES (ENTREPRENEURSHIP) ── --}}
        <div class="mb-16">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 border-b pb-4 border-gray-100 reveal-on-scroll">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <i class="bi bi-shop text-uco-orange-500"></i>
                        <span>My Ventures (Entrepreneurship)</span>
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Businesses you own, operate, or co-founded.</p>
                </div>
                <div>
                    <a href="{{ route('businesses.create') }}" class="btn-uco btn-uco-primary inline-flex items-center gap-2">
                        <i class="bi bi-plus-circle-fill"></i>
                        Register New Business
                    </a>
                </div>
            </div>

            @if($myBusinesses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($myBusinesses as $b)
                        @php $delay = ($loop->index % 12) * 50; @endphp
                        <a href="{{ route('businesses.show', $b) }}" 
                           class="group bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-xl hover:border-uco-orange-300 transition-all duration-500 overflow-hidden flex flex-col reveal-on-scroll" 
                           style="transition-delay: {{ $delay }}ms;">
                            {{-- Cover Image --}}
                            <div class="h-48 bg-gray-50 relative overflow-hidden">
                                @php
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

                                {{-- Status Badge --}}
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
                                        {{ optional($b->category)->name ?? 'Uncategorized' }}
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
                </div>
            @else
                <div class="bg-white border-2 border-dashed border-gray-200 rounded-2xl p-16 text-center reveal-on-scroll">
                    <div class="w-20 h-20 bg-soft-gray-50 text-soft-gray-300 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                        <i class="bi bi-shop-window text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Ventures Registered</h3>
                    <p class="text-gray-500 max-w-sm mx-auto leading-relaxed italic text-sm">
                        You haven't registered any businesses yet. Start showcasing your ventures to the UCO community!
                    </p>
                </div>
            @endif
        </div>

        {{-- ── SECTION 2: MY EMPLOYMENT CAREER (INTRAPRENEURSHIP) ── --}}
        <div>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 border-b pb-4 border-gray-100 reveal-on-scroll">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <i class="bi bi-briefcase text-uco-orange-500"></i>
                        <span>My Professional Career (Intrapreneurship)</span>
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Companies and corporate environments where you are currently employed as a professional.</p>
                </div>
                <div>
                    <a href="{{ route('intrapreneurs.create') }}" class="btn-uco btn-uco-primary inline-flex items-center gap-2">
                        <i class="bi bi-plus-circle-fill"></i>
                        Register Work Profile
                    </a>
                </div>
            </div>

            @if($myCompanies->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($myCompanies as $c)
                        @php $delay = ($loop->index % 12) * 50; @endphp
                        <a href="{{ route('intrapreneurs.show', $c) }}" 
                           class="group bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-xl hover:border-uco-orange-300 transition-all duration-500 overflow-hidden flex flex-col reveal-on-scroll" 
                           style="transition-delay: {{ $delay }}ms;">
                            
                            {{-- Cover Card --}}
                            <div class="h-48 bg-gradient-to-br from-indigo-50/50 to-purple-50/50 relative overflow-hidden flex items-center justify-center">
                                <i class="bi bi-building text-6xl text-slate-300/70 group-hover:scale-110 transition-transform duration-700"></i>
                                
                                {{-- Logo Overlay --}}
                                <div class="absolute bottom-4 left-4">
                                    @php $logo = $c->logo_url ? storage_image_url($c->logo_url, 'logo_thumb') : null; @endphp
                                    @if($logo)
                                        <img src="{{ $logo }}" class="w-16 h-16 rounded-xl bg-white p-1.5 shadow-lg border border-white object-cover">
                                    @endif
                                </div>

                                {{-- Status Badge --}}
                                <div class="absolute top-4 left-4 flex gap-2">
                                    <div class="px-2.5 py-1 rounded-lg shadow-sm flex items-center gap-1.5 text-[10px] font-black uppercase bg-emerald-500 text-white">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Active
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 flex-1 flex flex-col">
                                <div class="mb-4 text-left">
                                    <span class="inline-block px-2.5 py-1 rounded-lg bg-soft-gray-100 text-soft-gray-600 text-[10px] font-bold uppercase tracking-wider mb-2">
                                        {{ optional($c->category)->name ?? 'Corporate' }}
                                    </span>
                                    <h3 class="text-xl font-bold text-gray-900 group-hover:text-uco-orange-600 transition-colors line-clamp-1">{{ $c->name }}</h3>
                                </div>

                                <div class="space-y-1.5 mb-4 text-left">
                                    <p class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                        <i class="bi bi-person-badge text-slate-400"></i>
                                        <span>{{ $c->position }}</span>
                                    </p>
                                    @if($c->year_started_working)
                                        <p class="text-[11px] text-slate-400 font-semibold flex items-center gap-1.5">
                                            <i class="bi bi-calendar3"></i>
                                            <span>Started working: {{ $c->year_started_working }}</span>
                                        </p>
                                    @endif
                                </div>
                                
                                <p class="text-sm text-gray-500 line-clamp-2 mb-4 flex-1 italic text-left">
                                    {{ $c->job_description ?: 'No work description provided' }}
                                </p>

                                <div class="flex items-center justify-end pt-5 border-t border-gray-100 mt-auto">
                                    <div class="text-uco-orange-500 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all">
                                        <i class="bi bi-arrow-right-circle-fill text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="bg-white border-2 border-dashed border-gray-200 rounded-2xl p-16 text-center reveal-on-scroll">
                    <div class="w-20 h-20 bg-soft-gray-50 text-soft-gray-300 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                        <i class="bi bi-briefcase text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Work Profiles Registered</h3>
                    <p class="text-gray-500 max-w-sm mx-auto leading-relaxed italic text-sm">
                        Showcase where you currently work and make your professional career impact visible to the UCO community!
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
