{{-- --- Ventures an admin featured --- --}}
<x-ui.section title="Featured ventures" lead="Businesses run by our students and alumni." :href="route('businesses.index')" link-label="See all businesses">
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($spotlightBusinesses as $business)
            <x-business.card :business="$business" :href="route('businesses.show', $business)" />
        @empty
            <x-ui.empty-state class="col-span-full" title="No featured ventures yet">Ventures picked by the admin team will show here.</x-ui.empty-state>
        @endforelse
    </div>
</x-ui.section>
