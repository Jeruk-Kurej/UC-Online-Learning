<x-app-layout>
    @section('title', 'About Us')

    <div class="relative overflow-hidden bg-white pb-16 font-sans">
        @if (auth()->check() && auth()->user()->isAdmin())
            <div class="relative z-20 mx-auto flex max-w-[1600px] justify-end px-4 pt-4 sm:px-6 lg:px-8">
                <a href="{{ route('pages.edit', 'about') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2 text-xs font-bold text-white shadow-sm transition-all hover:bg-gray-800">
                    <i class="bi bi-pencil-square"></i> Edit About Page (CMS)
                </a>
            </div>
        @endif

        {{-- --- Dynamic Renderer for Sections --- --}}
        @foreach ($sections as $sec)
            {{-- --- Section type: hero --- --}}
            @if (($sec['type'] ?? '') === 'hero')
                <section class="relative overflow-hidden border-b border-orange-100/60 bg-gradient-to-b from-amber-50/70 via-white to-orange-50/40 px-4 py-16 sm:px-6 lg:px-8">
                    <div class="reveal-on-scroll relative z-10 mx-auto max-w-[1600px] text-center">
                        @if (!empty($sec['badge']))
                            <div class="mb-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-orange-200 bg-orange-50 px-3.5 py-1.5 text-xs font-bold text-orange-700 shadow-sm">
                                    <i class="bi bi-info-circle-fill text-orange-500"></i> {{ $sec['badge'] }}
                                </span>
                            </div>
                        @endif
                        <h1 class="mx-auto mb-6 max-w-4xl text-4xl font-bold leading-tight text-gray-950 md:text-6xl">
                            {{ $sec['title'] ?? '' }}
                        </h1>
                        <p class="mx-auto max-w-2xl text-base font-medium leading-relaxed text-gray-600 md:text-lg">
                            {{ $sec['subtitle'] ?? '' }}
                        </p>
                    </div>
                </section>

                {{-- --- Section type: feature cards grid --- --}}
            @elseif(($sec['type'] ?? '') === 'feature_cards')
                <section class="relative overflow-hidden bg-white px-4 py-16 sm:px-6 md:py-20 lg:px-8">
                    <div class="relative z-10 mx-auto max-w-[1600px]">
                        <div class="reveal-on-scroll mx-auto mb-14 max-w-3xl space-y-3 text-center">
                            @if (!empty($sec['badge']))
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-orange-200 bg-orange-50 px-3.5 py-1.5 text-xs font-bold text-orange-700">
                                    {{ $sec['badge'] }}
                                </span>
                            @endif
                            <h2 class="text-3xl font-bold text-gray-900 md:text-4xl">
                                {{ $sec['title'] ?? '' }}
                            </h2>
                            <p class="text-base font-medium leading-relaxed text-gray-500">
                                {{ $sec['subtitle'] ?? '' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            @foreach ($sec['cards'] ?? [] as $card)
                                <div class="reveal-on-scroll space-y-5 rounded-2xl border border-gray-200 bg-white p-8 shadow-sm transition-all duration-300 hover:border-orange-200 hover:shadow-md">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-xl border border-orange-100 bg-orange-50 text-2xl text-orange-600 shadow-sm">
                                        <i class="bi {{ $card['icon'] ?? 'bi-rocket-takeoff' }}"></i>
                                    </div>
                                    <h3 class="text-xl font-bold leading-snug text-gray-900">{{ $card['title'] ?? '' }}</h3>
                                    <p class="text-sm font-normal leading-relaxed text-gray-500">{{ $card['description'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- --- Section type: stats grid --- --}}
            @elseif(($sec['type'] ?? '') === 'stats_grid')
                <section class="mx-auto max-w-[1600px] px-4 py-16 sm:px-6 md:py-20 lg:px-8">
                    <div class="relative overflow-hidden rounded-3xl border border-slate-800 bg-slate-900 p-10 text-white shadow-sm md:p-16">
                        <div class="reveal-on-scroll relative z-10 text-center">
                            <div class="relative mb-12 inline-block">
                                <h2 class="text-2xl font-bold text-white md:text-4xl">{{ $sec['title'] ?? '' }}</h2>
                            </div>
                            <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                                @foreach ($sec['items'] ?? [] as $st)
                                    <div class="space-y-1">
                                        <p class="text-3xl font-bold text-[#f7931e] md:text-5xl">{{ $st['number'] ?? '' }}</p>
                                        <p class="text-xs font-bold text-gray-400">{{ $st['label'] ?? '' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>

                {{-- --- Section type: text / faq block --- --}}
            @elseif(($sec['type'] ?? '') === 'text_block')
                <section class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
                    <h2 class="mb-4 text-2xl font-bold text-gray-900 md:text-3xl">{{ $sec['heading'] ?? '' }}</h2>
                    <p class="whitespace-pre-line text-base font-normal leading-relaxed text-gray-600">{{ $sec['content'] ?? '' }}</p>
                </section>

                {{-- --- Section type: cta banner --- --}}
            @elseif(($sec['type'] ?? '') === 'cta_banner')
                <section class="mx-auto max-w-[1600px] px-4 py-16 sm:px-6 lg:px-8">
                    <div class="reveal-on-scroll relative overflow-hidden rounded-3xl border border-slate-800 bg-slate-900 p-10 text-center text-white shadow-sm md:p-16">
                        <div class="relative z-10 mx-auto max-w-3xl space-y-6">
                            <h2 class="text-3xl font-bold leading-tight md:text-4xl">
                                {{ $sec['heading'] ?? '' }}
                            </h2>
                            <p class="mx-auto max-w-2xl text-base font-normal leading-relaxed text-slate-300">
                                {{ $sec['subtitle'] ?? '' }}
                            </p>
                            <div class="flex flex-wrap justify-center gap-4 pt-2">
                                @if (!empty($sec['primary_btn_text']))
                                    <a href="{{ route('login') }}" class="rounded-xl bg-gradient-to-r from-[#f7931e] to-[#fdb913] px-7 py-3.5 font-bold text-white shadow-sm transition-all duration-200 hover:from-[#e0831a] hover:to-[#e6a600] hover:shadow-md">
                                        {{ $sec['primary_btn_text'] }}
                                    </a>
                                @endif
                                @if (!empty($sec['secondary_btn_text']))
                                    <a href="{{ route('businesses.index') }}" class="rounded-xl border border-white/20 bg-white/10 px-7 py-3.5 font-bold text-white transition-all duration-200 hover:bg-white/20">
                                        {{ $sec['secondary_btn_text'] }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        @endforeach
    </div>
</x-app-layout>
