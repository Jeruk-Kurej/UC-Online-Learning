<x-app-layout>
    @section('title', 'About Us')

    @if (auth()->check() && auth()->user()->isAdmin())
        <div class="mb-6 flex justify-end">
            <a href="{{ route('pages.edit', 'about') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Edit this page
            </a>
        </div>
    @endif

    <div class="space-y-14">
        @foreach ($sections as $sec)
            {{-- --- Section type: hero --- --}}
            @if (($sec['type'] ?? '') === 'hero')
                <header class="max-w-3xl">
                    @if (!empty($sec['badge']))
                        <p class="mb-2 text-base font-medium text-gray-600">{{ $sec['badge'] }}</p>
                    @endif
                    <h1 class="page-title !text-4xl sm:!text-5xl">{{ $sec['title'] ?? '' }}</h1>
                    <p class="mt-4 text-lg text-gray-700">{{ $sec['subtitle'] ?? '' }}</p>
                </header>

                {{-- --- Section type: feature cards --- --}}
            @elseif (($sec['type'] ?? '') === 'feature_cards')
                <x-ui.section :title="$sec['title'] ?? ''" :lead="$sec['subtitle'] ?? null">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                        @foreach ($sec['cards'] ?? [] as $card)
                            <article class="card">
                                <i class="bi {{ $card['icon'] ?? 'bi-star' }} text-3xl text-brand-700" aria-hidden="true"></i>
                                <h3 class="mt-3 text-lg font-semibold text-brand-900">{{ $card['title'] ?? '' }}</h3>
                                <p class="mt-1 text-base text-gray-700">{{ $card['description'] ?? '' }}</p>
                            </article>
                        @endforeach
                    </div>
                </x-ui.section>

                {{-- --- Section type: numbers --- --}}
            @elseif (($sec['type'] ?? '') === 'stats_grid')
                <x-ui.section :title="$sec['title'] ?? ''">
                    <dl class="grid grid-cols-2 gap-5 md:grid-cols-4">
                        @foreach ($sec['items'] ?? [] as $st)
                            <div class="card">
                                <dd class="font-display text-3xl font-semibold text-brand-900">{{ $st['number'] ?? '' }}</dd>
                                <dt class="mt-1 text-base text-gray-700">{{ $st['label'] ?? '' }}</dt>
                            </div>
                        @endforeach
                    </dl>
                </x-ui.section>

                {{-- --- Section type: text block --- --}}
            @elseif (($sec['type'] ?? '') === 'text_block')
                <section class="max-w-3xl">
                    <h2 class="section-heading">{{ $sec['heading'] ?? '' }}</h2>
                    <p class="mt-3 whitespace-pre-line text-lg text-gray-800">{{ $sec['content'] ?? '' }}</p>
                </section>

                {{-- --- Section type: call to action --- --}}
            @elseif (($sec['type'] ?? '') === 'cta_banner')
                <section class="card max-w-3xl">
                    <h2 class="section-heading">{{ $sec['heading'] ?? '' }}</h2>
                    <p class="mt-2 text-lg text-gray-700">{{ $sec['subtitle'] ?? '' }}</p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        @if (!empty($sec['primary_btn_text']))
                            <a href="{{ route('login') }}" class="btn btn-primary">{{ $sec['primary_btn_text'] }}</a>
                        @endif
                        @if (!empty($sec['secondary_btn_text']))
                            <a href="{{ route('businesses.index') }}" class="btn btn-secondary">{{ $sec['secondary_btn_text'] }}</a>
                        @endif
                    </div>
                </section>
            @endif
        @endforeach
    </div>
</x-app-layout>
