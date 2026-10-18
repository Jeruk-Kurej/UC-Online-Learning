<div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
    @forelse ($businesses as $business)
        <x-business.card :business="$business" :kind="$viewType" :href="$viewType === 'entrepreneur' ? route('businesses.show', $business) : route('intrapreneurs.show', $business)" />
    @empty
        <x-ui.empty-state class="col-span-full" title="Nothing matches your search">Try a different name, or clear the filters.</x-ui.empty-state>
    @endforelse
</div>

<div class="pagination-ajax mt-8">
    {{ $businesses->links() }}
</div>
