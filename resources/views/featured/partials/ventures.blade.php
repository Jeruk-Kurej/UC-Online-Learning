{{-- --- The other ventures an admin featured: with three or more, the first one is large and the rest sit beside it --- --}}
@if ($heroVenture === null)
    <x-ui.section title="Featured ventures" lead="Businesses run by our students and alumni." :href="route('businesses.index')" link-label="See all businesses">
        <x-ui.empty-state title="No featured ventures yet">Ventures picked by the admin team will show here.</x-ui.empty-state>
    </x-ui.section>
@elseif ($moreVentures->isNotEmpty())
    <x-ui.section title="More featured ventures" lead="Businesses run by our students and alumni." :href="route('businesses.index')" link-label="See all businesses">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($moreVentures as $venture)
                <div @class([
                    'lg:col-span-2' => $loop->first,
                    'lg:row-span-2' => $loop->first && $moreVentures->count() > 2,
                ])>
                    <x-featured.venture-tile :business="$venture" :large="$loop->first" />
                </div>
            @endforeach
        </div>
    </x-ui.section>
@endif
