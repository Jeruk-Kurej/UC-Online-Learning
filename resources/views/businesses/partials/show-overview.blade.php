{{-- --- Business page overview card: description, insights, operations and contact links --- --}}
<div class="overflow-hidden border border-soft-gray-100 bg-white shadow-lg sm:rounded-lg">

    {{-- --- Business Info Section --- --}}
    <div class="p-4 sm:p-6 lg:p-8">
        {{-- --- Description --- --}}
        <div class="mb-8">
            <h4 class="mb-3 text-sm font-bold uppercase tracking-wider text-soft-gray-900">About This
                Business</h4>
            <p
               class="w-full max-w-[1600px] text-base leading-relaxed text-soft-gray-700 2xl:max-w-[1720px]">
                {{ $business->description }}</p>
        </div>

        {{-- --- Business Insights --- --}}
        <div class="{{ $showRevenue ? 'sm:grid-cols-2' : 'sm:grid-cols-3' }} mb-4 grid grid-cols-1 gap-x-4 gap-y-6 border-y border-gray-100 py-8 md:gap-x-6 lg:gap-x-8">
            @can('update', $business)
                <x-business.insight class="border-gray-100 sm:border-r sm:pr-4 md:pr-6 lg:pr-8" icon="bi-cash-stack" tone="emerald" caption="Visible only to you" italic-caption>
                    <x-slot:label class="flex items-center gap-1.5"><i class="bi bi-lock-fill text-[9px]"></i> Private Revenue</x-slot:label>
                    {{ $business->revenue_range ?? 'Not Disclosed' }}
                </x-business.insight>
            @endcan

            <x-business.insight :class="$showRevenue ? '' : 'sm:border-r border-gray-100 sm:pr-4 md:pr-6 lg:pr-8'" icon="bi-people-fill" tone="blue" caption="Active staff">
                <x-slot:label>Team Size</x-slot:label>
                {{ $business->employee_count ?? '0' }} Professionals
            </x-business.insight>

            <x-business.insight class="border-gray-100 sm:border-r sm:pr-4 md:pr-6 lg:pr-8" icon="bi-rocket-takeoff-fill" tone="indigo" caption="Operational milestone" italic-caption>
                <x-slot:label>Venture Inception</x-slot:label>
                @if ($business->established_date)
                    Est. {{ \Carbon\Carbon::parse($business->established_date)->format('M Y') }}
                @else
                    Timeline Undisclosed
                @endif
            </x-business.insight>

            <x-business.insight icon="bi-mortarboard-fill" tone="orange" caption="Founding academic Join UC Online">
                <x-slot:label>Academic Heritage</x-slot:label>
                {{ $business->academic_heritage ?? 'UCO Legacy' }}
            </x-business.insight>
        </div>

        {{-- --- Comprehensive Business Details --- --}}
        <div class="mb-12">
            <h4 class="mb-6 text-sm font-bold uppercase tracking-wider text-soft-gray-900">Business Operations & Details</h4>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                {{-- --- Target Market --- --}}
                <div class="space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Target Market</p>
                    <p class="text-sm font-normal text-gray-800">{{ $business->target_market ?: 'Not specified' }}</p>
                </div>

                {{-- --- Customer Base --- --}}
                <div class="space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Customer Base Size</p>
                    <p class="text-sm font-bold text-gray-800">{{ $business->customer_base_size ?: 'Not specified' }}</p>
                </div>

                {{-- --- Scale --- --}}
                <div class="space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Business Scale</p>
                    <p class="text-sm font-bold text-gray-800">{{ $business->business_scale ?: 'Not specified' }}</p>
                </div>

                {{-- --- Location --- --}}
                <div class="space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Location</p>
                    <p class="text-sm font-bold text-gray-800">
                        {{ $business->city ? $business->city . ($business->province ? ', ' . $business->province : '') : ($business->province ?: 'Not specified') }}
                    </p>
                </div>

                {{-- --- Legality --- --}}
                <div class="space-y-1 border-t border-gray-100 pt-3 md:col-span-2">
                    <p class="mb-2 text-[10px] font-bold uppercase tracking-widest text-gray-400">Legality</p>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-2 rounded-lg border border-gray-100 bg-white px-3 py-1.5 shadow-sm">
                            <i class="bi bi-briefcase text-gray-400"></i>
                            <span class="text-xs font-bold text-gray-700">Business: <span class="font-medium">{{ $business->business_legality ?: 'None' }}</span></span>
                        </div>
                        <div class="flex items-center gap-2 rounded-lg border border-gray-100 bg-white px-3 py-1.5 shadow-sm">
                            <i class="bi bi-box-seam text-gray-400"></i>
                            <span class="text-xs font-bold text-gray-700">Product: <span class="font-medium">{{ $business->product_legality ?: 'None' }}</span></span>
                        </div>
                    </div>
                </div>

                {{-- --- Connect & Contacts --- --}}
                @if ($business->website || $business->email || $business->instagram || $business->whatsapp)
                    <div class="space-y-2 border-t border-gray-100 pt-3 md:col-span-2">
                        <p class="mb-3 text-[10px] font-bold uppercase tracking-widest text-gray-400">Connect</p>
                        <div class="flex flex-wrap gap-3">
                            @if ($business->website)
                                <a href="{{ str_starts_with($business->website, 'http') ? $business->website : 'https://' . $business->website }}" target="_blank" class="flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-gray-700 transition-colors hover:bg-gray-100">
                                    <i class="bi bi-globe text-gray-500"></i>
                                    <span class="text-xs font-bold">{{ preg_replace('#^https?://#', '', $business->website) }}</span>
                                </a>
                            @endif
                            @if ($business->whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}" target="_blank" class="flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-green-700 transition-colors hover:bg-green-100">
                                    <i class="bi bi-whatsapp"></i>
                                    <span class="text-xs font-bold">{{ $business->whatsapp }}</span>
                                </a>
                            @endif
                            @foreach ($business->instagram_handles as $handle)
                                <a href="https://instagram.com/{{ $handle }}" target="_blank" class="flex items-center gap-2 rounded-lg border border-pink-200 bg-pink-50 px-3 py-1.5 text-pink-700 transition-colors hover:bg-pink-100">
                                    <i class="bi bi-instagram"></i>
                                    <span class="text-xs font-bold">@ {{ $handle }}</span>
                                </a>
                            @endforeach
                            @if ($business->email)
                                <a href="mailto:{{ $business->email }}" class="flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-blue-700 transition-colors hover:bg-blue-100">
                                    <i class="bi bi-envelope-fill"></i>
                                    <span class="text-xs font-bold">{{ $business->email }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
