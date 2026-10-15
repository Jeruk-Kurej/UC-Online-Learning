<x-app-layout>
    @section('title', $business->name . ' - ' . ($business->category->name ?? 'Venture'))
    <div data-business-show x-data="businessShowPage">
        @include('businesses.partials.show-header')

        {{-- ═══ 2-COLUMN LAYOUT ═══ --}}
        <div class="grid grid-cols-1 gap-6 md:gap-8 items-start" id="biz-layout-grid">
        {{-- LEFT COLUMN: Business Content --}}
        <div class="space-y-6 min-w-0">
            @include('businesses.partials.show-overview')

            {{-- ═══ FLOWING SECTIONS: Products → Services ═══ --}}
            <div class="mt-4 space-y-6">

                {{-- ── PRODUCTS SECTION ── --}}
                @if ($offersProducts && ($products->isNotEmpty() || $canManageBusiness))
                    <x-business.offerings-section :business="$business" type="product" :offerings="$products" :can-manage="$canManageBusiness"
                        eyebrow="What We Offer" empty-message="No products added yet. Start building your product catalog." group-actions />
                @endif

                {{-- ── SERVICES SECTION ── --}}
                @if ($offersServices && ($services->count() > 0 || $canManageBusiness))
                    <x-business.offerings-section :business="$business" type="service" :offerings="$services" :can-manage="$canManageBusiness"
                        eyebrow="Expert Solutions" empty-message="No services added yet. Add your first service to showcase what you offer." />
                @endif


                {{-- Nothing to show at all --}}
                @if ($products->isEmpty() && !$canManageBusiness)
                    <div class="bg-white shadow-lg sm:rounded-lg border border-soft-gray-100">
                        <div class="text-center py-14 px-6">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-gray-100">
                                <i class="bi bi-inbox text-3xl text-gray-300"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 mb-2">Nothing to show yet</h4>
                            <p class="text-sm text-gray-500 max-w-md mx-auto">
                                This business hasn't added any products, gallery photos, or contact entries yet.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @include('businesses.partials.show-detail-modal')


        {{-- ═══ RIGHT COLUMN (35%) ═══ --}}
        <div class="md:sticky md:top-6 min-w-0 flex flex-col gap-6">

            @include('businesses.partials.owners', ['owner' => $business->public_owner, 'coOwners' => $business->public_co_owners])

        </div>
        {{-- END RIGHT COLUMN --}}

        </div>
        {{-- END 2-COLUMN GRID --}}

    </div>


</x-app-layout>
