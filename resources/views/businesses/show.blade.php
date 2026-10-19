<x-app-layout>
    @section('title', $business->name . ' - ' . ($business->category->name ?? 'Venture'))
    <div data-business-show x-data="businessShowPage">
        @include('businesses.partials.show-header')

        <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-3">
            {{-- --- Main column --- --}}
            <div class="min-w-0 space-y-10 lg:col-span-2">
                @include('businesses.partials.show-overview')

                @if ($offersProducts && ($products->isNotEmpty() || $canManageBusiness))
                    <x-business.offerings-section :business="$business" type="product" :offerings="$products" :can-manage="$canManageBusiness" empty-message="No products yet. Add the first one to start your catalog." />
                @endif

                @if ($offersServices && ($services->isNotEmpty() || $canManageBusiness))
                    <x-business.offerings-section :business="$business" type="service" :offerings="$services" :can-manage="$canManageBusiness" empty-message="No services yet. Add the first one to show what you offer." />
                @endif

                @if ($products->isEmpty() && $services->isEmpty() && !$canManageBusiness)
                    <x-ui.empty-state title="Nothing listed yet">This business has not added any products or services.</x-ui.empty-state>
                @endif
            </div>

            {{-- --- Side column --- --}}
            <div class="min-w-0 space-y-6">
                @include('businesses.partials.owners', ['owner' => $business->public_owner, 'coOwners' => $business->public_co_owners])
            </div>
        </div>

        @include('businesses.partials.show-detail-modal')
    </div>
</x-app-layout>
