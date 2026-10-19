{{-- --- One group of the community on the home page (ventures, entrepreneurs or intrapreneurs): title, short line, an optional link and a grid of photo panels; nothing is drawn when the group is empty --- --}}
@props(['items', 'title', 'accent', 'lead', 'href' => null, 'linkLabel' => null])

@if ($items->isNotEmpty())
    <x-ui.section :title="$title" :accent="$accent" :lead="$lead" :href="$href" :link-label="$linkLabel">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            {{ $slot }}
        </div>
    </x-ui.section>
@endif
