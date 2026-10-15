<x-app-layout>
    <div class="mx-auto w-full max-w-[1600px] px-4 py-8 sm:px-6 lg:px-8 2xl:max-w-[1720px]">
        {{-- --- Page Header --- --}}
        <div class="reveal-on-scroll mb-12">
            <h1 class="mb-2 text-4xl font-extrabold tracking-tight text-gray-900">My Business & Career Portfolio</h1>
            <p class="max-w-3xl text-lg leading-relaxed text-gray-500">
                Manage and showcase your entrepreneurial ventures and professional employment profiles within the UCO community.
            </p>
        </div>

        {{-- --- Section 1: my ventures (entrepreneurship) --- --}}
        <div class="mb-16">
            <div class="reveal-on-scroll mb-8 flex flex-col justify-between gap-4 border-b border-gray-100 pb-4 sm:flex-row sm:items-center">
                <div>
                    <h2 class="flex items-center gap-2 text-2xl font-bold text-gray-900">
                        <i class="bi bi-shop text-uco-orange-500"></i>
                        <span>My Ventures (Entrepreneurship)</span>
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">Businesses you own, operate, or co-founded.</p>
                </div>
                <div>
                    <a href="{{ route('businesses.create') }}" class="btn-uco btn-uco-primary inline-flex items-center gap-2">
                        <i class="bi bi-plus-circle-fill"></i>
                        Register New Business
                    </a>
                </div>
            </div>

            @if ($myBusinesses->count() > 0)
                <div class="reveal-stagger-12 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($myBusinesses as $b)
                        <a href="{{ route('businesses.show', $b) }}"
                           class="reveal-on-scroll group flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-all duration-500 hover:border-uco-orange-300 hover:shadow-xl">

                            {{-- --- Card Header --- --}}
                            <div class="flex items-start gap-4">
                                {{-- --- Logo Thumbnail --- --}}
                                <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-100 bg-gray-50 shadow-sm">
                                    @if ($b->logo_thumb_url)
                                        <img src="{{ $b->logo_thumb_url }}" class="h-full w-full object-contain">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 text-gray-300">
                                            <span class="select-none text-2xl font-black opacity-30">{{ substr($b->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- --- Titles --- --}}
                                <div class="min-w-0 flex-1 text-left">
                                    <h3 class="mb-1 line-clamp-2 text-lg font-bold leading-tight text-gray-900 transition-colors group-hover:text-uco-orange-600">{{ $b->name }}</h3>
                                    <span class="inline-block rounded-lg bg-soft-gray-100 px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-soft-gray-600">
                                        {{ optional($b->category)->name ?? 'Uncategorized' }}
                                    </span>
                                </div>
                            </div>

                            {{-- --- Badges Row --- --}}
                            <div class="mt-4 flex items-center gap-2">
                                {{-- --- Status Badge --- --}}
                                <span class="{{ $b->status === 'approved' ? 'bg-green-500 text-white' : ($b->status === 'rejected' ? 'bg-red-500 text-white' : 'bg-uco-orange-500 text-white') }} flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-[9px] font-black uppercase tracking-wider shadow-sm">
                                    <i class="bi {{ $b->status === 'approved' ? 'bi-check-circle-fill' : ($b->status === 'rejected' ? 'bi-x-circle-fill' : 'bi-hourglass-split') }} text-[9px]"></i>
                                    {{ $b->status_label }}
                                </span>

                                {{-- --- Featured Badge --- --}}
                                @if ($b->is_featured && $b->status === 'approved')
                                    <span class="flex items-center gap-1.5 rounded-lg bg-yellow-400 px-2.5 py-1 text-[9px] font-black uppercase tracking-wider text-yellow-900 shadow-sm">
                                        <i class="bi bi-star-fill text-[9px]"></i> Featured
                                    </span>
                                @endif
                            </div>

                            {{-- --- Description --- --}}
                            <p class="my-4 line-clamp-3 flex-1 text-left text-sm italic leading-relaxed text-gray-500">
                                {{ $b->description ?: 'No description provided' }}
                            </p>

                            {{-- --- Rejection / Revision reason --- --}}
                            @if (in_array($b->status, ['rejected', 'need_revision']) && $b->rejection_reason)
                                <div class="{{ $b->status === 'rejected' ? 'bg-red-50 border-red-100' : 'bg-blue-50 border-blue-100' }} mb-4 rounded-xl border p-3 text-left">
                                    <p class="{{ $b->status === 'rejected' ? 'text-red-600' : 'text-blue-600' }} mb-1 text-[10px] font-bold uppercase tracking-wider">
                                        {{ $b->status === 'rejected' ? 'Rejection Reason:' : 'Revision Feedback:' }}
                                    </p>
                                    <p class="{{ $b->status === 'rejected' ? 'text-red-700' : 'text-blue-700' }} text-xs italic">"{{ $b->rejection_reason }}"</p>
                                </div>
                            @endif

                            {{-- --- Arrow link footer --- --}}
                            <div class="mt-auto flex items-center justify-end border-t border-gray-50 pt-4">
                                <div class="text-uco-orange-500 opacity-0 transition-all group-hover:translate-x-1 group-hover:opacity-100">
                                    <i class="bi bi-arrow-right-circle-fill text-xl"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="reveal-on-scroll rounded-2xl border-2 border-dashed border-gray-200 bg-white p-16 text-center">
                    <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-soft-gray-50 text-soft-gray-300 shadow-inner">
                        <i class="bi bi-shop-window text-4xl"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-gray-900">No Ventures Registered</h3>
                    <p class="mx-auto max-w-sm text-sm italic leading-relaxed text-gray-500">
                        You haven't registered any businesses yet. Start showcasing your ventures to the UCO community!
                    </p>
                </div>
            @endif
        </div>

        {{-- --- Section 2: my employment career (intrapreneurship) --- --}}
        <div>
            <div class="reveal-on-scroll mb-8 flex flex-col justify-between gap-4 border-b border-gray-100 pb-4 sm:flex-row sm:items-center">
                <div>
                    <h2 class="flex items-center gap-2 text-2xl font-bold text-gray-900">
                        <i class="bi bi-briefcase text-uco-orange-500"></i>
                        <span>My Professional Career (Intrapreneurship)</span>
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">Companies and corporate environments where you are currently employed as a professional.</p>
                </div>
                <div>
                    <a href="{{ route('intrapreneurs.create') }}" class="btn-uco btn-uco-primary inline-flex items-center gap-2">
                        <i class="bi bi-plus-circle-fill"></i>
                        Register Work Profile
                    </a>
                </div>
            </div>

            @if ($myCompanies->count() > 0)
                <div class="reveal-stagger-12 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($myCompanies as $c)
                        <a href="{{ route('intrapreneurs.show', $c) }}"
                           class="reveal-on-scroll group flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-all duration-500 hover:border-uco-orange-300 hover:shadow-xl">

                            {{-- --- Card Header --- --}}
                            <div class="flex items-start gap-4">
                                {{-- --- Logo Thumbnail --- --}}
                                <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-100 bg-gray-50 shadow-sm">
                                    @if ($c->logo_thumb_url)
                                        <img src="{{ $c->logo_thumb_url }}" class="h-full w-full object-contain">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 text-gray-300">
                                            <span class="select-none text-2xl font-black opacity-30">{{ substr($c->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- --- Titles --- --}}
                                <div class="min-w-0 flex-1 text-left">
                                    <h3 class="mb-1 line-clamp-2 text-lg font-bold leading-tight text-gray-900 transition-colors group-hover:text-uco-orange-600">{{ $c->name }}</h3>
                                    <span class="inline-block rounded-lg bg-soft-gray-100 px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-soft-gray-600">
                                        {{ optional($c->category)->name ?? 'Corporate' }}
                                    </span>
                                </div>
                            </div>

                            {{-- --- Badges Row --- --}}
                            <div class="mt-4 flex items-center gap-2">
                                {{-- --- Status Badge --- --}}
                                <span class="flex items-center gap-1.5 rounded-lg bg-emerald-500 px-2.5 py-1 text-[9px] font-black uppercase tracking-wider text-white shadow-sm">
                                    <i class="bi bi-check-circle-fill text-[9px]"></i>
                                    Active
                                </span>
                            </div>

                            {{-- --- Metadata --- --}}
                            <div class="my-4 flex-1 space-y-1.5 text-left">
                                <p class="flex items-center gap-1.5 text-sm font-extrabold text-slate-700">
                                    <i class="bi bi-person-badge text-slate-400"></i>
                                    <span>{{ $c->position }}</span>
                                </p>
                                @if ($c->year_started_working)
                                    <p class="flex items-center gap-1.5 text-xs font-semibold text-slate-400">
                                        <i class="bi bi-calendar-check text-slate-400"></i>
                                        <span>Started: {{ $c->year_started_working }}</span>
                                    </p>
                                @endif
                                @if ($c->company_scale)
                                    <p class="flex items-center gap-1.5 text-xs font-semibold text-slate-400">
                                        <i class="bi bi-diagram-3 text-slate-400"></i>
                                        <span>Scale: {{ $c->company_scale }}</span>
                                    </p>
                                @endif
                            </div>

                            {{-- --- Arrow link footer --- --}}
                            <div class="mt-auto flex items-center justify-end border-t border-gray-50 pt-4">
                                <div class="text-uco-orange-500 opacity-0 transition-all group-hover:translate-x-1 group-hover:opacity-100">
                                    <i class="bi bi-arrow-right-circle-fill text-xl"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="reveal-on-scroll rounded-2xl border-2 border-dashed border-gray-200 bg-white p-16 text-center">
                    <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-soft-gray-50 text-soft-gray-300 shadow-inner">
                        <i class="bi bi-briefcase text-4xl"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-gray-900">No Work Profiles Registered</h3>
                    <p class="mx-auto max-w-sm text-sm italic leading-relaxed text-gray-500">
                        Showcase where you currently work and make your professional career impact visible to the UCO community!
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
