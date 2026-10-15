{{-- --- Business page overview card: description, insights, operations and contact links --- --}}
<div class="bg-white shadow-lg sm:rounded-lg overflow-hidden border border-soft-gray-100">


    {{-- --- Business Info Section --- --}}
    <div class="p-4 sm:p-6 lg:p-8">
        {{-- --- Description --- --}}
        <div class="mb-8">
            <h4 class="text-sm font-bold text-soft-gray-900 uppercase tracking-wider mb-3">About This
                Business</h4>
            <p
                class="text-base text-soft-gray-700 leading-relaxed w-full max-w-[1600px] 2xl:max-w-[1720px]">
                {{ $business->description }}</p>
        </div>

        {{-- --- Business Insights --- --}}
        <div class="grid grid-cols-1 {{ $showRevenue ? 'sm:grid-cols-2' : 'sm:grid-cols-3' }} gap-y-6 gap-x-4 md:gap-x-6 lg:gap-x-8 py-8 mb-4 border-y border-gray-100">
            {{-- --- Revenue (SENSITIVE - PROTECTED) --- --}}
            @can('update', $business)
            <div class="flex items-start gap-4 sm:gap-2 md:gap-4 sm:border-r border-gray-100 sm:pr-4 md:pr-6 lg:pr-8">
                <div class="w-11 h-11 sm:w-9 sm:h-9 md:w-11 md:h-11 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100/50 shadow-sm flex-shrink-0">
                    <i class="bi bi-cash-stack text-lg sm:text-sm md:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1.5 flex items-center gap-1.5">
                        <i class="bi bi-lock-fill text-[9px]"></i> Private Revenue
                    </p>
                    <h5 class="text-sm sm:text-xs md:text-sm lg:text-base font-extrabold text-gray-800">{{ $business->revenue_range ?? 'Not Disclosed' }}</h5>
                    <p class="text-[9px] text-gray-400 font-medium mt-0.5 italic">Visible only to you</p>
                </div>
            </div>
            @endcan

            {{-- --- Employee Count --- --}}
            <div class="flex items-start gap-4 sm:gap-2 md:gap-4 {{ $showRevenue ? '' : 'sm:border-r border-gray-100 sm:pr-4 md:pr-6 lg:pr-8' }}">
                <div class="w-11 h-11 sm:w-9 sm:h-9 md:w-11 md:h-11 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100/50 shadow-sm flex-shrink-0">
                    <i class="bi bi-people-fill text-lg sm:text-sm md:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1.5">Team Size</p>
                    <h5 class="text-sm sm:text-xs md:text-sm lg:text-base font-extrabold text-gray-800">{{ $business->employee_count ?? '0' }} Professionals</h5>
                    <p class="text-[9px] text-gray-400 font-medium mt-0.5">Active staff</p>
                </div>
            </div>

            {{-- --- Venture Inception --- --}}
            <div class="flex items-start gap-4 sm:gap-2 md:gap-4 sm:border-r border-gray-100 sm:pr-4 md:pr-6 lg:pr-8">
                <div class="w-11 h-11 sm:w-9 sm:h-9 md:w-11 md:h-11 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100/50 shadow-sm flex-shrink-0">
                    <i class="bi bi-rocket-takeoff-fill text-lg sm:text-sm md:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1.5">Venture Inception</p>
                    <h5 class="text-sm sm:text-xs md:text-sm lg:text-base font-extrabold text-gray-800">
                        @if($business->established_date)
                            Est. {{ \Carbon\Carbon::parse($business->established_date)->format('M Y') }}
                        @else
                            Timeline Undisclosed
                        @endif
                    </h5>
                    <p class="text-[9px] text-gray-400 font-medium mt-0.5 italic">Operational milestone</p>
                </div>
            </div>

            {{-- --- Academic Heritage --- --}}
            <div class="flex items-start gap-4 sm:gap-2 md:gap-4">
                <div class="w-11 h-11 sm:w-9 sm:h-9 md:w-11 md:h-11 rounded-lg bg-orange-50 text-uco-orange-600 flex items-center justify-center border border-orange-100/50 shadow-sm flex-shrink-0">
                    <i class="bi bi-mortarboard-fill text-lg sm:text-sm md:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1.5">Academic Heritage</p>
                    <h5 class="text-sm sm:text-xs md:text-sm lg:text-base font-extrabold text-gray-800">{{ $business->academic_heritage ?? 'UCO Legacy' }}</h5>
                    <p class="text-[9px] text-gray-400 font-medium mt-0.5">Founding academic Join UC Online</p>
                </div>
            </div>
        </div>

        {{-- --- Comprehensive Business Details --- --}}
        <div class="mb-12">
            <h4 class="text-sm font-bold text-soft-gray-900 uppercase tracking-wider mb-6">Business Operations & Details</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- --- Target Market --- --}}
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Target Market</p>
                    <p class="text-sm font-normal text-gray-800">{{ $business->target_market ?: 'Not specified' }}</p>
                </div>

                {{-- --- Customer Base --- --}}
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Customer Base Size</p>
                    <p class="text-sm font-bold text-gray-800">{{ $business->customer_base_size ?: 'Not specified' }}</p>
                </div>

                {{-- --- Scale --- --}}
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Business Scale</p>
                    <p class="text-sm font-bold text-gray-800">{{ $business->business_scale ?: 'Not specified' }}</p>
                </div>

                {{-- --- Location --- --}}
                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Location</p>
                    <p class="text-sm font-bold text-gray-800">
                        {{ $business->city ? $business->city . ($business->province ? ', ' . $business->province : '') : ($business->province ?: 'Not specified') }}
                    </p>
                </div>

                {{-- --- Legality --- --}}
                <div class="space-y-1 md:col-span-2 pt-3 border-t border-gray-100">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Legality</p>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-gray-100 shadow-sm">
                            <i class="bi bi-briefcase text-gray-400"></i>
                            <span class="text-xs font-bold text-gray-700">Business: <span class="font-medium">{{ $business->business_legality ?: 'None' }}</span></span>
                        </div>
                        <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-gray-100 shadow-sm">
                            <i class="bi bi-box-seam text-gray-400"></i>
                            <span class="text-xs font-bold text-gray-700">Product: <span class="font-medium">{{ $business->product_legality ?: 'None' }}</span></span>
                        </div>
                    </div>
                </div>

                {{-- --- Connect & Contacts --- --}}
                @if($business->website || $business->email || $business->instagram || $business->whatsapp)
                <div class="space-y-2 md:col-span-2 pt-3 border-t border-gray-100">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Connect</p>
                    <div class="flex flex-wrap gap-3">
                        @if($business->website)
                            <a href="{{ str_starts_with($business->website, 'http') ? $business->website : 'https://' . $business->website }}" target="_blank" class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-700 transition-colors border border-gray-200">
                                <i class="bi bi-globe text-gray-500"></i>
                                <span class="text-xs font-bold">{{ preg_replace('#^https?://#', '', $business->website) }}</span>
                            </a>
                        @endif
                        @if($business->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}" target="_blank" class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-green-50 hover:bg-green-100 text-green-700 transition-colors border border-green-200">
                                <i class="bi bi-whatsapp"></i>
                                <span class="text-xs font-bold">{{ $business->whatsapp }}</span>
                            </a>
                        @endif
                        @foreach($business->instagram_handles as $handle)
                            <a href="https://instagram.com/{{ $handle }}" target="_blank" class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-pink-50 hover:bg-pink-100 text-pink-700 transition-colors border border-pink-200">
                                <i class="bi bi-instagram"></i>
                                <span class="text-xs font-bold">@ {{ $handle }}</span>
                            </a>
                        @endforeach
                        @if($business->email)
                            <a href="mailto:{{ $business->email }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 transition-colors border border-blue-200">
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
