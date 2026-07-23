<x-app-layout>
    @section('title', 'About Us')

    @php
        $content = is_array($page?->content_json) ? $page->content_json : (json_decode($page?->content_json ?? '[]', true) ?: []);
        $blocks = $content['blocks'] ?? [];
    @endphp

    <div class="relative overflow-hidden font-sans bg-white">
        @if(auth()->check() && auth()->user()->isAdmin())
            <div class="flex justify-end max-w-[1600px] mx-auto px-6 pt-4 relative z-20">
                <a href="{{ route('pages.edit', 'about') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800 text-xs font-extrabold shadow-md transition-all">
                    <i class="bi bi-pencil-square"></i> Edit About Page (CMS)
                </a>
            </div>
        @endif

        @if(!empty($blocks))
            {{-- Dynamic CMS Block Engine --}}
            <section class="py-16 px-6 max-w-[1200px] mx-auto">
                <div class="space-y-8">
                    @foreach($blocks as $block)
                        @php
                            $type = $block['type'] ?? 'paragraph';
                            $data = $block['data'] ?? [];
                        @endphp

                        @if($type === 'header')
                            @php $level = $data['level'] ?? 2; @endphp
                            @if($level === 1)
                                <h1 class="text-4xl md:text-6xl font-black text-gray-950 tracking-tight mt-10 mb-6 leading-tight">{!! $data['text'] ?? '' !!}</h1>
                            @elseif($level === 2)
                                <h2 class="text-3xl md:text-5xl font-black text-gray-900 tracking-tight mt-12 mb-6 leading-tight">{!! $data['text'] ?? '' !!}</h2>
                            @elseif($level === 3)
                                <h3 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight mt-8 mb-4 leading-tight">{!! $data['text'] ?? '' !!}</h3>
                            @else
                                <h4 class="text-xl font-bold text-gray-900 mt-6 mb-3">{!! $data['text'] ?? '' !!}</h4>
                            @endif

                        @elseif($type === 'paragraph')
                            <p class="text-base md:text-lg text-gray-600 font-medium leading-relaxed mb-6">{!! $data['text'] ?? '' !!}</p>

                        @elseif($type === 'list')
                            @php $style = $data['style'] ?? 'unordered'; @endphp
                            @if($style === 'ordered')
                                <ol class="list-decimal list-inside space-y-2 mb-6 font-medium text-gray-700 text-base md:text-lg">
                                    @foreach(($data['items'] ?? []) as $item)
                                        <li>{!! is_array($item) ? ($item['content'] ?? '') : $item !!}</li>
                                    @endforeach
                                </ol>
                            @else
                                <ul class="list-disc list-inside space-y-2 mb-6 font-medium text-gray-700 text-base md:text-lg">
                                    @foreach(($data['items'] ?? []) as $item)
                                        <li>{!! is_array($item) ? ($item['content'] ?? '') : $item !!}</li>
                                    @endforeach
                                </ul>
                            @endif

                        @elseif($type === 'image')
                            @php 
                                $imgUrl = $data['file']['url'] ?? ($data['url'] ?? '');
                                $caption = $data['caption'] ?? '';
                            @endphp
                            @if($imgUrl)
                                <figure class="my-8">
                                    <img src="{{ $imgUrl }}" alt="{{ strip_tags($caption) }}" class="rounded-3xl shadow-lg border border-slate-100 max-h-[600px] w-full object-cover">
                                    @if($caption)
                                        <figcaption class="text-center text-xs font-semibold text-slate-400 mt-3">{!! $caption !!}</figcaption>
                                    @endif
                                </figure>
                            @endif

                        @elseif($type === 'embed')
                            @php $embedUrl = $data['embed'] ?? ($data['source'] ?? ''); @endphp
                            @if($embedUrl)
                                <div class="my-8 aspect-video rounded-3xl overflow-hidden shadow-lg border border-slate-100">
                                    <iframe src="{{ $embedUrl }}" class="w-full h-full" allowfullscreen></iframe>
                                </div>
                            @endif

                        @elseif($type === 'raw')
                            <div class="my-8">
                                {!! $data['html'] ?? '' !!}
                            </div>

                        @elseif($type === 'quote')
                            <blockquote class="my-8 border-l-4 border-uco-orange-500 pl-6 py-4 bg-orange-50/50 rounded-r-2xl text-lg italic text-slate-800 font-semibold">
                                {!! $data['text'] ?? '' !!}
                                @if(!empty($data['caption']))
                                    <cite class="block text-xs not-italic font-bold text-slate-400 uppercase tracking-widest mt-2">{!! $data['caption'] !!}</cite>
                                @endif
                            </blockquote>

                        @elseif($type === 'delimiter')
                            <hr class="my-12 border-slate-200">

                        @elseif($type === 'table')
                            <div class="my-8 overflow-x-auto rounded-2xl border border-slate-200 shadow-sm">
                                <table class="w-full text-left text-sm text-slate-700">
                                    <tbody>
                                        @foreach(($data['content'] ?? []) as $row)
                                            <tr class="border-b border-slate-100 last:border-b-0">
                                                @foreach($row as $cell)
                                                    <td class="px-6 py-4 font-medium">{!! $cell !!}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endforeach
                </div>
            </section>
        @else
            {{-- Default Visual Layout Fallback --}}
            <section class="relative pt-12 pb-16 px-6 overflow-hidden bg-gradient-to-b from-orange-50/60 to-white">
                <div class="uco-hero-mesh"></div>
                <div class="max-w-[1600px] mx-auto text-center relative z-10 reveal-on-scroll">
                    <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-4 py-1.5 text-xs font-black uppercase tracking-widest text-uco-orange-600 mb-6">
                        About UC Online Learning
                    </span>
                    <h1 class="text-4xl md:text-6xl lg:text-7xl font-black text-gray-950 tracking-tight mb-6">
                        Building the Future of <br class="hidden sm:inline">
                        <span class="text-uco-orange-500">Student & Alumni Entrepreneurship</span>
                    </h1>
                    <p class="max-w-3xl mx-auto text-base md:text-lg text-gray-600 leading-relaxed font-medium">
                        Connecting founders, intrapreneurs, and corporate innovators across Universitas Ciputra.
                    </p>
                </div>
            </section>

            {{-- Pillars of Excellence --}}
            <section class="py-20 bg-white px-6 relative overflow-hidden">
                <div class="max-w-[1600px] mx-auto relative z-10">
                    <div class="text-center max-w-3xl mx-auto mb-16 space-y-4 reveal-on-scroll">
                        <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-4 py-1.5 text-xs font-extrabold uppercase tracking-widest text-uco-orange-600">
                            Pillars of Excellence
                        </span>
                        <h2 class="text-3xl md:text-5xl font-black text-gray-900 tracking-tight">
                            Built for Sustainable Impact
                        </h2>
                        <p class="text-base md:text-lg text-gray-500 font-medium">
                            Designed to support founders and intrapreneurs at every phase of their growth journey.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="space-y-6 reveal-on-scroll p-8 rounded-3xl border border-gray-100 bg-white shadow-sm hover:shadow-xl transition-all duration-300">
                            <div class="w-16 h-16 bg-uco-orange-50 text-uco-orange-500 rounded-2xl flex items-center justify-center text-3xl shadow-sm border border-uco-orange-100">
                                <i class="bi bi-rocket-takeoff"></i>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900">Rapid Launch</h3>
                            <p class="text-gray-500 leading-relaxed font-medium">We provide the tools and network needed to transform academic theories into viable market products within weeks, not years.</p>
                        </div>
                        <div class="space-y-6 reveal-on-scroll p-8 rounded-3xl border border-gray-100 bg-white shadow-sm hover:shadow-xl transition-all duration-300">
                            <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-3xl shadow-sm border border-blue-100">
                                <i class="bi bi-people"></i>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900">Global Network</h3>
                            <p class="text-gray-500 leading-relaxed font-medium">Connect with a diverse community of alumni mentors, industry experts, and fellow entrepreneurs across all major industries.</p>
                        </div>
                        <div class="space-y-6 reveal-on-scroll p-8 rounded-3xl border border-gray-100 bg-white shadow-sm hover:shadow-xl transition-all duration-300">
                            <div class="w-16 h-16 bg-purple-50 text-purple-500 rounded-2xl flex items-center justify-center text-3xl shadow-sm border border-purple-100">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900">Scalable Growth</h3>
                            <p class="text-gray-500 leading-relaxed font-medium">From local startups to multinational enterprises, our platform supports scaling businesses at every stage of their lifecycle.</p>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </div>
</x-app-layout>
