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
                           class="group bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-xl hover:border-uco-orange-300 transition-all duration-500 overflow-hidden flex flex-col p-6 reveal-on-scroll" 
                           style="transition-delay: {{ $delay }}ms;">
                            
                            {{-- Card Header --}}
                            <div class="flex items-start gap-4">
                                {{-- Logo Thumbnail --}}
                                <div class="w-16 h-16 bg-gray-50 rounded-xl flex items-center justify-center border border-gray-100 flex-shrink-0 overflow-hidden shadow-sm">
                                    @php $logo = $b->logo_url ? storage_image_url($b->logo_url, 'logo_thumb') : null; @endphp
                                    @if($logo)
                                        <img src="{{ $logo }}" class="w-full h-full object-contain">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-300">
                                            <span class="text-2xl font-black opacity-30 select-none">{{ substr($b->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Titles --}}
                                <div class="flex-1 min-w-0 text-left">
                                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-uco-orange-600 transition-colors line-clamp-2 leading-tight mb-1">{{ $b->name }}</h3>
                                    <span class="inline-block px-2.5 py-0.5 rounded-lg bg-soft-gray-100 text-soft-gray-600 text-[9px] font-bold uppercase tracking-wider">
                                        {{ optional($b->category)->name ?? 'Uncategorized' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Badges Row --}}
                            <div class="flex items-center gap-2 mt-4">
                                {{-- Status Badge --}}
                                <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider flex items-center gap-1.5 shadow-sm
                                    {{ $b->status === 'approved' ? 'bg-green-500 text-white' : ($b->status === 'rejected' ? 'bg-red-500 text-white' : 'bg-uco-orange-500 text-white') }}">
                                    <i class="bi {{ $b->status === 'approved' ? 'bi-check-circle-fill' : ($b->status === 'rejected' ? 'bi-x-circle-fill' : 'bi-hourglass-split') }} text-[9px]"></i>
                                    {{ $b->status_label }}
                                </span>

                                {{-- Featured Badge --}}
                                @if($b->is_featured && $b->status === 'approved')
                                    <span class="bg-yellow-400 text-yellow-900 text-[9px] font-black uppercase tracking-wider px-2.5 py-1 rounded-lg shadow-sm flex items-center gap-1.5">
                                        <i class="bi bi-star-fill text-[9px]"></i> Featured
                                    </span>
                                @endif
                            </div>

                            {{-- Description --}}
                            <p class="text-sm text-gray-500 line-clamp-3 my-4 flex-1 italic text-left leading-relaxed">
                                {{ $b->description ?: 'No description provided' }}
                            </p>

                            {{-- Rejection / Revision reason --}}
                            @if(in_array($b->status, ['rejected', 'need_revision']) && $b->rejection_reason)
                                <div class="mb-4 p-3 {{ $b->status === 'rejected' ? 'bg-red-50 border-red-100' : 'bg-blue-50 border-blue-100' }} border rounded-xl text-left">
                                    <p class="text-[10px] font-bold {{ $b->status === 'rejected' ? 'text-red-600' : 'text-blue-600' }} uppercase tracking-wider mb-1">
                                        {{ $b->status === 'rejected' ? 'Rejection Reason:' : 'Revision Feedback:' }}
                                    </p>
                                    <p class="text-xs {{ $b->status === 'rejected' ? 'text-red-700' : 'text-blue-700' }} italic">"{{ $b->rejection_reason }}"</p>
                                </div>
                            @endif

                            {{-- Arrow link footer --}}
                            <div class="flex items-center justify-end pt-4 border-t border-gray-50 mt-auto">
                                <div class="text-uco-orange-500 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all">
                                    <i class="bi bi-arrow-right-circle-fill text-xl"></i>
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
                           class="group bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-xl hover:border-uco-orange-300 transition-all duration-500 overflow-hidden flex flex-col p-6 reveal-on-scroll" 
                           style="transition-delay: {{ $delay }}ms;">
                            
                            {{-- Card Header --}}
                            <div class="flex items-start gap-4">
                                {{-- Logo Thumbnail --}}
                                <div class="w-16 h-16 bg-gray-50 rounded-xl flex items-center justify-center border border-gray-100 flex-shrink-0 overflow-hidden shadow-sm">
                                    @php $logo = $c->logo_url ? storage_image_url($c->logo_url, 'logo_thumb') : null; @endphp
                                    @if($logo)
                                        <img src="{{ $logo }}" class="w-full h-full object-contain">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-300">
                                            <span class="text-2xl font-black opacity-30 select-none">{{ substr($c->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Titles --}}
                                <div class="flex-1 min-w-0 text-left">
                                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-uco-orange-600 transition-colors line-clamp-2 leading-tight mb-1">{{ $c->name }}</h3>
                                    <span class="inline-block px-2.5 py-0.5 rounded-lg bg-soft-gray-100 text-soft-gray-600 text-[9px] font-bold uppercase tracking-wider">
                                        {{ optional($c->category)->name ?? 'Corporate' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Badges Row --}}
                            <div class="flex items-center gap-2 mt-4">
                                {{-- Status Badge --}}
                                <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider flex items-center gap-1.5 shadow-sm bg-emerald-500 text-white">
                                    <i class="bi bi-check-circle-fill text-[9px]"></i>
                                    Active
                                </span>
                            </div>

                            {{-- Metadata --}}
                            <div class="space-y-1.5 my-4 text-left flex-1">
                                <p class="text-sm font-extrabold text-slate-700 flex items-center gap-1.5">
                                    <i class="bi bi-person-badge text-slate-400"></i>
                                    <span>{{ $c->position }}</span>
                                </p>
                                @if($c->year_started_working)
                                    <p class="text-xs text-slate-400 font-semibold flex items-center gap-1.5">
                                        <i class="bi bi-calendar-check text-slate-400"></i>
                                        <span>Started: {{ $c->year_started_working }}</span>
                                    </p>
                                @endif
                                @if($c->company_scale)
                                    <p class="text-xs text-slate-400 font-semibold flex items-center gap-1.5">
                                        <i class="bi bi-diagram-3 text-slate-400"></i>
                                        <span>Scale: {{ $c->company_scale }}</span>
                                    </p>
                                @endif
                            </div>

                            {{-- Arrow link footer --}}
                            <div class="flex items-center justify-end pt-4 border-t border-gray-50 mt-auto">
                                <div class="text-uco-orange-500 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all">
                                    <i class="bi bi-arrow-right-circle-fill text-xl"></i>
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
