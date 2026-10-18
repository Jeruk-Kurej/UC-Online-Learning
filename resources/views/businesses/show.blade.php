<x-app-layout>
    @section('title', $business->name . ' - ' . ($business->category->name ?? 'Venture'))
    <div data-business-show x-data="businessShowPage">
        @include('businesses.partials.show-header')

        {{-- --- 2-column layout --- --}}
        <div class="grid grid-cols-1 items-start gap-6 md:gap-8" id="biz-layout-grid">
            {{-- --- LEFT COLUMN: Business Content --- --}}
            <div class="min-w-0 space-y-6">
                @include('businesses.partials.show-overview')

                {{-- --- FLOWING SECTIONS: Products → Services --- --}}
                <div class="mt-4 space-y-6">

                    {{-- --- Products section --- --}}
                    @if ($offersProducts && ($products->isNotEmpty() || $canManageBusiness))
                        <x-business.offerings-section :business="$business" type="product" :offerings="$products" :can-manage="$canManageBusiness"
                                                      eyebrow="What We Offer" empty-message="No products added yet. Start building your product catalog." />
                    @endif

                    {{-- --- Services section --- --}}
                    @if ($offersServices && ($services->isNotEmpty() || $canManageBusiness))
                        <x-business.offerings-section :business="$business" type="service" :offerings="$services" :can-manage="$canManageBusiness"
                                                      eyebrow="Expert Solutions" empty-message="No services added yet. Add your first service to showcase what you offer." />
                    @endif

                    {{-- --- Nothing to show at all --- --}}
                    @if ($products->isEmpty() && !$canManageBusiness)
                        <div class="border border-soft-gray-100 bg-white shadow-lg sm:rounded-lg">
                            <div class="px-6 py-14 text-center">
                                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full border border-gray-100 bg-white shadow-sm">
                                    <i class="bi bi-inbox text-3xl text-gray-600"></i>
                                </div>
                                <h4 class="mb-2 text-lg font-bold text-gray-900">Nothing to show yet</h4>
                                <p class="mx-auto max-w-md text-sm text-gray-600">
                                    This business hasn't added any products, gallery photos, or contact entries yet.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @include('businesses.partials.show-detail-modal')

            {{-- --- Right column (35%) --- --}}
            <div class="flex min-w-0 flex-col gap-6 md:sticky md:top-6">

                @include('businesses.partials.owners', ['owner' => $business->public_owner, 'coOwners' => $business->public_co_owners])

            </div>
            {{-- --- End right column --- --}}

        </div>
        {{-- --- End 2-column grid --- --}}

    </div>

</x-app-layout>
