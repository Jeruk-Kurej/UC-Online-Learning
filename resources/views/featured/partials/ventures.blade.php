{{-- --- Ventures an admin featured: all equal, in an order that changes daily --- --}}
<x-ui.section title="Featured ventures" lead="Businesses run by our students and alumni." :href="route('businesses.index')" link-label="See all businesses">
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($featuredVentures as $venture)
            <x-featured.venture-tile :business="$venture" />
        @empty
            <x-ui.empty-state class="col-span-full" title="No featured ventures yet">Ventures picked by the admin team will show here.</x-ui.empty-state>
        @endforelse
    </div>
</x-ui.section>
