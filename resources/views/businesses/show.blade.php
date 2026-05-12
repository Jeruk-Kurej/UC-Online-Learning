@use('Illuminate\Support\Facades\Storage')

<x-app-layout>
    @php
        $canManageBusiness = auth()->check() && $business->canBeManagedBy(auth()->user());
    @endphp
    @php
        $businessTypeName = $business->category?->name ?? ucfirst(str_replace('_', ' ', $business->offering_type ?? 'business'));
        $businessMode = $business->offering_type ?? 'product';
        $isProductMode = $businessMode === 'product';
        $isServiceMode = $businessMode === 'service';
        $isBothMode = $businessMode === 'both';
        $businessModeLabel = $isBothMode ? 'Products & Services' : ($isProductMode ? 'Product-Based' : 'Service-Based');
        $businessModeShortLabel = $isBothMode ? 'Both' : ($isProductMode ? 'Product' : 'Service');
        $businessProducts = collect($business->products ?? []);
        $businessPhotos = collect($business->photos ?? []);
        $businessServices = collect();
        $businessContacts = collect();
        $hasProducts = $businessProducts->isNotEmpty();
        $hasGallery = $businessPhotos->isNotEmpty();
        $hasContacts = $businessContacts->isNotEmpty();
        $visibleSectionCount = collect([$hasProducts, $hasGallery, $hasContacts])->filter()->count();
        $activeTabDefault = $hasProducts ? 'products' : ($hasGallery ? 'photos' : ($hasContacts ? 'contacts' : 'products'));
        $hasProductCategoriesRoute = \Illuminate\Support\Facades\Route::has('businesses.product-categories.index');
        $hasProductsCreateRoute = \Illuminate\Support\Facades\Route::has('businesses.products.create');
        $hasProductsShowRoute = \Illuminate\Support\Facades\Route::has('businesses.products.show');
        $hasServicesCreateRoute = \Illuminate\Support\Facades\Route::has('businesses.services.create');
        $hasServicesShowRoute = \Illuminate\Support\Facades\Route::has('businesses.services.show');
        $hasServicesEditRoute = \Illuminate\Support\Facades\Route::has('businesses.services.edit');
        $hasServicesDestroyRoute = \Illuminate\Support\Facades\Route::has('businesses.services.destroy');
        $hasPhotosIndexRoute = \Illuminate\Support\Facades\Route::has('businesses.photos.index');
        $hasPhotosCreateRoute = \Illuminate\Support\Facades\Route::has('businesses.photos.create');
        $hasContactsCreateRoute = \Illuminate\Support\Facades\Route::has('businesses.contacts.create');
        $hasContactsEditRoute = \Illuminate\Support\Facades\Route::has('businesses.contacts.edit');
        $hasContactsDestroyRoute = \Illuminate\Support\Facades\Route::has('businesses.contacts.destroy');
    @endphp
    <div x-data="{
        showUserModal: false,
        fullscreenOpen: false,
        fullscreenSrc: '',
        fullscreenAlt: '',
        openFullscreen(src, alt = 'Photo') {
            this.fullscreenSrc = src;
            this.fullscreenAlt = alt;
            this.fullscreenOpen = true;
        }
    }" x-init="$watch('showUserModal', val => document.body.style.overflow = val ? 'hidden' : '');
    $watch('fullscreenOpen', val => { if (val) { document.body.style.overflow = 'hidden'; } else if (!showUserModal) { document.body.style.overflow = ''; } })">
        {{-- Hero Section with Elegant Back Button --}}
        <div class="mb-8 px-4 sm:px-0">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6">

                <div class="flex-1 flex flex-row items-center sm:items-start gap-4 sm:gap-5">
                    {{-- Business Logo --}}
                    @if ($business->logo_url)
                        <img src="{{ storage_image_url($business->logo_url, ['width' => 256, 'height' => 256, 'crop' => 'thumb', 'quality' => 'auto', 'fetch_format' => 'auto']) }}"
                            alt="{{ $business->name }} Logo" loading="lazy"
                            class="w-16 h-16 sm:w-20 sm:h-20 flex-shrink-0 rounded-lg object-cover border border-soft-gray-100 shadow-sm bg-white p-0.5">
                    @else
                        <div
                            class="w-16 h-16 sm:w-20 sm:h-20 flex-shrink-0 rounded-lg bg-gradient-to-br from-soft-gray-50 to-soft-gray-100 border border-soft-gray-100 flex items-center justify-center shadow-sm">
                            <i class="bi bi-briefcase text-2xl sm:text-3xl text-soft-gray-300"></i>
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-xl {{ strtolower($business->type ?? '') === 'entrepreneur' ? 'bg-orange-100 text-orange-700' : 'bg-indigo-100 text-indigo-700' }}">
                                <i class="bi bi-person-workspace text-[10px]"></i>
                                {{ ucfirst($business->type ?? 'Entrepreneur') }}
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 bg-soft-gray-100 text-soft-gray-700 text-xs font-semibold rounded-xl max-w-[220px]"
                                title="{{ $businessTypeName }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span class="truncate">{{ $businessTypeName }}</span>
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-xl
                            {{ $isBothMode ? 'bg-purple-100 text-purple-700' : ($isProductMode ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if ($isBothMode)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z" />
                                    @elseif($isProductMode)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    @endif
                                </svg>
                                <span class="hidden sm:inline">{{ $businessModeLabel }}</span>
                                <span class="sm:hidden">{{ $businessModeShortLabel }}</span>
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-soft-gray-900 tracking-tight leading-tight">
                            {{ $business->name }}</h1>
                    </div>
                </div>
                @auth
                    @if ($canManageBusiness)
                        <a href="{{ route('businesses.edit', $business) }}"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-soft-gray-900 hover:bg-soft-gray-800 text-white rounded-xl font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span class="hidden sm:inline">Edit Business</span>
                            <span class="sm:hidden">Edit</span>
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        {{-- ═══ 2-COLUMN LAYOUT ═══ --}}
        <div class="grid grid-cols-1 gap-6 md:gap-8 items-start" id="biz-layout-grid">
        {{-- LEFT COLUMN: Business Content --}}
        <div class="space-y-6 min-w-0">
            {{-- Business Overview Card - Professional Design --}}
            <div class="bg-white shadow-lg sm:rounded-lg overflow-hidden border border-soft-gray-100">
                {{-- Hero Photo Carousel (Dynamic & Premium) --}}
                <div class="relative h-64 sm:h-72 lg:h-80 overflow-hidden group"
                    @php $heroPhotosCount = $businessPhotos->count(); @endphp x-data="{
                        activeHeroSlide: 0,
                        heroSlidesCount: {{ $heroPhotosCount }},
                        heroTimer: null,
                        startHeroTimer() {
                            if (this.heroSlidesCount > 1) {
                                this.heroTimer = setInterval(() => {
                                    this.activeHeroSlide = (this.activeHeroSlide + 1) % this.heroSlidesCount;
                                }, 5000);
                            }
                        },
                        stopHeroTimer() {
                            if (this.heroTimer) clearInterval(this.heroTimer);
                        }
                    }"
                    x-init="startHeroTimer()" @mouseenter="stopHeroTimer()" @mouseleave="startHeroTimer()">

                    @forelse($businessPhotos as $index => $photo)
                        <div x-show="activeHeroSlide === {{ $index }}"
                            x-transition:enter="transition ease-out duration-1000"
                            x-transition:enter-start="opacity-0 scale-105"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-1000" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0" class="absolute inset-0 w-full h-full">
                            <img src="{{ storage_image_url($photo->photo_url, 'hero') }}"
                                alt="{{ $business->name }} - Hero Photo {{ $index + 1 }}"
                                class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent">
                            </div>
                        </div>
                    @empty
                        <div
                            class="w-full h-full bg-gradient-to-br from-soft-gray-100 via-soft-gray-50 to-soft-gray-100 flex items-center justify-center relative">
                            <svg class="w-24 h-24 text-soft-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/5 to-transparent">
                            </div>
                        </div>
                    @endforelse

                    {{-- Hero Controls (only if multiple) --}}
                    @if ($heroPhotosCount > 1)
                        {{-- Arrows --}}
                        <button
                            @click="activeHeroSlide = (activeHeroSlide - 1 + {{ $heroPhotosCount }}) % {{ $heroPhotosCount }}"
                            class="absolute left-6 top-1/2 -translate-y-1/2 z-20 w-12 h-12 flex items-center justify-center rounded-full bg-black/30 hover:bg-black/60 text-white opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-all duration-300 backdrop-blur-md border border-white/20 hover:scale-110">
                            <i class="bi bi-chevron-left text-2xl"></i>
                        </button>
                        <button @click="activeHeroSlide = (activeHeroSlide + 1) % {{ $heroPhotosCount }}"
                            class="absolute right-6 top-1/2 -translate-y-1/2 z-20 w-12 h-12 flex items-center justify-center rounded-full bg-black/30 hover:bg-black/60 text-white opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-all duration-300 backdrop-blur-md border border-white/20 hover:scale-110">
                            <i class="bi bi-chevron-right text-2xl"></i>
                        </button>

                        {{-- Hero Dots indicator (centered) --}}
                        <div class="absolute bottom-8 left-0 right-0 flex justify-center gap-2.5 z-20">
                            @foreach ($businessPhotos as $index => $photo)
                                <button @click="activeHeroSlide = {{ $index }}"
                                    class="h-1.5 rounded-full transition-all duration-500 shadow-lg"
                                    :class="activeHeroSlide === {{ $index }} ? 'w-10 bg-white' :
                                        'w-2.5 bg-white/40 hover:bg-white/60 backdrop-blur-sm'"></button>
                            @endforeach
                        </div>
                    @endif
                </div>


                {{-- Business Info Section --}}
                <div class="p-4 sm:p-6 lg:p-8">
                    {{-- Description --}}
                    <div class="mb-8">
                        <h4 class="text-sm font-bold text-soft-gray-900 uppercase tracking-wider mb-3">About This
                            Business</h4>
                        <p
                            class="text-base text-soft-gray-700 leading-relaxed w-full max-w-[1600px] 2xl:max-w-[1720px]">
                            {{ $business->description }}</p>
                    </div>

                    {{-- Business Insights - Professional Dossier Metadata (REDESIGNED) --}}
                    <div class="flex flex-wrap items-center gap-y-6 gap-x-12 py-8 mb-4 border-y border-gray-100">
                        {{-- Revenue (SENSITIVE - PROTECTED) --}}
                        @can('update', $business)
                        <div class="flex items-start gap-4 group">
                            <div class="w-11 h-11 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100/50 shadow-sm group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                                <i class="bi bi-cash-stack text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1.5 flex items-center gap-1.5">
                                    <i class="bi bi-lock-fill text-[9px]"></i> Private Revenue
                                </p>
                                <h5 class="text-base font-extrabold text-gray-800">{{ $business->revenue_range ?? 'Not Disclosed' }}</h5>
                                <p class="text-[9px] text-gray-400 font-medium mt-0.5 italic">Visible only to you</p>
                            </div>
                        </div>
                        <div class="hidden lg:block w-px h-10 bg-gray-100"></div>
                        @endcan

                        {{-- Employee Count --}}
                        <div class="flex items-start gap-4 group">
                            <div class="w-11 h-11 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100/50 shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                                <i class="bi bi-people-fill text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1.5">Team Size</p>
                                <h5 class="text-base font-extrabold text-gray-800">{{ $business->employee_count ?? '0' }} Professionals</h5>
                                <p class="text-[9px] text-gray-400 font-medium mt-0.5">Permanent & active staff</p>
                            </div>
                        </div>

                        <div class="hidden lg:block w-px h-10 bg-gray-100"></div>

                        {{-- Venture Inception --}}
                        <div class="flex items-start gap-4 group">
                            <div class="w-11 h-11 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100/50 shadow-sm group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                                <i class="bi bi-rocket-takeoff-fill text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1.5">Venture Inception</p>
                                <h5 class="text-base font-extrabold text-gray-800">
                                    @if($business->established_date)
                                        Est. {{ \Carbon\Carbon::parse($business->established_date)->format('M Y') }}
                                    @else
                                        Timeline Undisclosed
                                    @endif
                                </h5>
                                <p class="text-[9px] text-gray-400 font-medium mt-0.5 italic">Operational milestone</p>
                            </div>
                        </div>

                        <div class="hidden lg:block w-px h-10 bg-gray-100"></div>

                        {{-- Academic Heritage --}}
                        <div class="flex items-start gap-4 group">
                            <div class="w-11 h-11 rounded-lg bg-orange-50 text-uco-orange-600 flex items-center justify-center border border-orange-100/50 shadow-sm group-hover:bg-uco-orange-600 group-hover:text-white transition-all duration-300">
                                <i class="bi bi-mortarboard-fill text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1.5">Academic Heritage</p>
                                <h5 class="text-base font-extrabold text-gray-800">{{ $business->academic_heritage ?? 'UCO Legacy' }}</h5>
                                <p class="text-[9px] text-gray-400 font-medium mt-0.5">Founding academic batch</p>
                            </div>
                        </div>
                    </div>

                    {{-- Comprehensive Business Details --}}
                    <div class="mb-12">
                        <h4 class="text-sm font-bold text-soft-gray-900 uppercase tracking-wider mb-6">Business Operations & Details</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50/50 p-6 rounded-2xl border border-gray-100">
                            {{-- Target Market --}}
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Target Market</p>
                                <p class="text-sm font-bold text-gray-800">{{ $business->target_market ?: 'Not specified' }}</p>
                            </div>

                            {{-- Customer Base --}}
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Customer Base Size</p>
                                <p class="text-sm font-bold text-gray-800">{{ $business->customer_base_size ?: 'Not specified' }}</p>
                            </div>

                            {{-- Scale --}}
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Business Scale</p>
                                <p class="text-sm font-bold text-gray-800">{{ $business->business_scale ?: 'Not specified' }}</p>
                            </div>

                            {{-- Location --}}
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Location</p>
                                <p class="text-sm font-bold text-gray-800">
                                    {{ $business->city ? $business->city . ($business->province ? ', ' . $business->province : '') : ($business->province ?: 'Not specified') }}
                                </p>
                            </div>

                            {{-- Legality --}}
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

                            {{-- Connect & Contacts --}}
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
                                    @if($business->instagram)
                                        <a href="https://instagram.com/{{ ltrim($business->instagram, '@') }}" target="_blank" class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-pink-50 hover:bg-pink-100 text-pink-700 transition-colors border border-pink-200">
                                            <i class="bi bi-instagram"></i>
                                            <span class="text-xs font-bold">{{ $business->instagram }}</span>
                                        </a>
                                    @endif
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

                    {{-- Academic Origin / Success Badge --}}
                    @if($business->is_from_college_project)
                    <div class="mb-12 p-1.5 inline-flex items-center gap-3 bg-gradient-to-r from-orange-50 to-white border border-orange-100 rounded-lg shadow-sm">
                        <div class="w-8 h-8 rounded-xl bg-uco-orange-500 text-white flex items-center justify-center shadow-md">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div class="pr-4">
                            <p class="text-[10px] font-bold text-uco-orange-700 uppercase tracking-widest leading-none">Corporate Origin</p>
                            <p class="text-[11px] text-uco-orange-900 font-extrabold mt-0.5">Evolution of UCO Entrepreneurship Project</p>
                        </div>
                        @if($business->is_continued_after_graduation)
                            <div class="h-6 w-px bg-orange-100 mx-1"></div>
                            <div class="flex items-center gap-1.5 pr-2">
                                <i class="bi bi-patch-check-fill text-indigo-500 text-sm"></i>
                                <span class="text-[10px] font-bold text-indigo-700 uppercase tracking-widest">Sustained Post-Graduation</span>
                            </div>
                        @endif
                    </div>
                    @endif


                    {{-- Original Documents Links (Hidden if empty or moved to Insights) --}}
                    @if ($business->legal_document_path || $business->certification_path)
                        <div class="mt-8 pt-6 border-t border-soft-gray-100 flex flex-wrap gap-6">
                            @if ($business->legal_document_path)
                                <a href="{{ Storage::url($business->legal_document_path) }}" target="_blank" class="flex items-center gap-3 group">
                                    <div class="w-10 h-10 rounded-lg bg-soft-gray-50 flex items-center justify-center text-soft-gray-400 group-hover:bg-red-50 group-hover:text-red-500 transition-colors">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-soft-gray-500 uppercase tracking-widest leading-none">Legal Document</p>
                                        <p class="text-xs font-bold text-soft-gray-700 group-hover:text-soft-gray-900 leading-none mt-1">View Document</p>
                                    </div>
                                </a>
                            @endif

                            @if ($business->certification_path)
                                <a href="{{ Storage::url($business->certification_path) }}" target="_blank" class="flex items-center gap-3 group">
                                    <div class="w-10 h-10 rounded-lg bg-soft-gray-50 flex items-center justify-center text-soft-gray-400 group-hover:bg-blue-50 group-hover:text-blue-500 transition-colors">
                                        <i class="bi bi-patch-check"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-soft-gray-500 uppercase tracking-widest leading-none">Product Cert.</p>
                                        <p class="text-xs font-bold text-soft-gray-700 group-hover:text-soft-gray-900 leading-none mt-1">View Certificate</p>
                                    </div>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>


            {{-- Tabs Navigation - Elegant Design --}}
            <div id="business-tabs" x-data="{
                activeTab: '{{ session('activeTab', $activeTabDefault) }}'
            }"
                class="mt-10 bg-white shadow-lg sm:rounded-lg border border-soft-gray-100">
                <div class="border-b-2 border-soft-gray-100">
                    <nav class="flex -mb-px px-6 overflow-x-auto">
                        @if ($isProductMode || $isBothMode)
                            <button @click="activeTab = 'products'"
                                @if (! $hasProducts) style="display: none;" @endif
                                :class="activeTab === 'products' ? 'border-soft-gray-900 text-soft-gray-900' :
                                    'border-transparent text-soft-gray-500 hover:text-soft-gray-700 hover:border-soft-gray-300'"
                                class="flex items-center gap-2 py-4 px-4 border-b-2 font-semibold text-sm transition duration-150 whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Products
                            </button>
                        @endif

                        @if ($isServiceMode || $isBothMode)
                            <button @click="activeTab = 'services'"
                                :class="activeTab === 'services' ? 'border-soft-gray-900 text-soft-gray-900' :
                                    'border-transparent text-soft-gray-500 hover:text-soft-gray-700 hover:border-soft-gray-300'"
                                class="flex items-center gap-2 py-4 px-4 border-b-2 font-semibold text-sm transition duration-150 whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                </svg>
                                Services
                            </button>
                        @endif

                        <button @click="activeTab = 'photos'"
                            @if (! $hasGallery) style="display: none;" @endif
                            :class="activeTab === 'photos' ? 'border-soft-gray-900 text-soft-gray-900' :
                                'border-transparent text-soft-gray-500 hover:text-soft-gray-700 hover:border-soft-gray-300'"
                            class="flex items-center gap-2 py-4 px-4 border-b-2 font-semibold text-sm transition duration-150 whitespace-nowrap">
                            <i class="bi bi-images text-base"></i>
                            Gallery
                        </button>

                        <button @click="activeTab = 'contacts'"
                            @if (! $hasContacts) style="display: none;" @endif
                            :class="activeTab === 'contacts' ? 'border-soft-gray-900 text-soft-gray-900' :
                                'border-transparent text-soft-gray-500 hover:text-soft-gray-700 hover:border-soft-gray-300'"
                            class="flex items-center gap-2 py-4 px-4 border-b-2 font-semibold text-sm transition duration-150 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Contacts

                        </button>
                    </nav>
                </div>

                @if ($visibleSectionCount > 0)
                    {{-- Tab: Products --}}
                    @if ($hasProducts)
                    <div x-show="activeTab === 'products'" class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Products</h3>
                            @auth
                                @if ($canManageBusiness && $businessProducts->count() > 0)
                                    <div class="flex items-center gap-2">
                                        @if ($hasProductCategoriesRoute)
                                            <a href="{{ route('businesses.product-categories.index', $business) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-uco-orange-500 hover:bg-uco-orange-600 text-white text-sm font-semibold rounded-xl transition">
                                                <i class="bi bi-tags"></i>
                                                Manage Categories
                                            </a>
                                        @endif
                                        @if ($hasProductsCreateRoute)
                                            <a href="{{ route('businesses.products.create', $business) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-soft-gray-900 hover:bg-soft-gray-800 text-white text-sm font-medium rounded-xl shadow-sm transition duration-150">
                                                <i class="bi bi-plus-lg"></i>
                                                Add Product
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            @endauth
                        </div>

                        @if ($businessProducts->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($businessProducts as $product)
                                    @php
                                        $productPhotos = collect($product->photos ?? []);
                                    @endphp
                                    <div @if ($hasProductsShowRoute) @click="if(!event.target.closest('button') && !event.target.closest('a')) window.location='{{ route('businesses.products.show', [$business, $product]) }}'" @endif
                                        class="group border border-gray-200 rounded-lg overflow-hidden hover:shadow-xl transition-all duration-300 {{ $hasProductsShowRoute ? 'cursor-pointer' : '' }} bg-white">
                                        {{-- Product Image / Carousel --}}
                                        <div class="relative overflow-hidden group" x-data="{
                                            currentIndex: 0,
                                            total: {{ $productPhotos->count() }},
                                            timer: null,
                                            startTimer() {
                                                if (this.total > 1) {
                                                    this.timer = setInterval(() => {
                                                        this.currentIndex = (this.currentIndex + 1) % this.total;
                                                    }, 4000);
                                                }
                                            },
                                            stopTimer() {
                                                if (this.timer) clearInterval(this.timer);
                                            }
                                        }"
                                            x-init="startTimer()" @mouseenter="stopTimer()"
                                            @mouseleave="startTimer()">

                                            @if ($productPhotos->count() > 0)
                                                {{-- Image Container --}}
                                                <div class="relative h-48 bg-gray-100">
                                                    @foreach ($productPhotos as $index => $photo)
                                                        <div x-show="currentIndex === {{ $index }}"
                                                            x-transition:enter="transition ease-out duration-500"
                                                            x-transition:enter-start="opacity-0 scale-105"
                                                            x-transition:enter-end="opacity-100 scale-100"
                                                            x-transition:leave="transition ease-in duration-300"
                                                            x-transition:leave-start="opacity-100"
                                                            x-transition:leave-end="opacity-0"
                                                            class="absolute inset-0 cursor-pointer">
                                                            <img src="{{ storage_image_url($photo->photo_url, 'gallery_thumb') }}"
                                                                alt="{{ $product->name }}"
                                                                class="w-full h-full object-cover">
                                                        </div>
                                                    @endforeach

                                                    {{-- Navigation Controls (only if multiple) --}}
                                                    @if ($productPhotos->count() > 1)
                                                        {{-- Arrows --}}
                                                        <button
                                                            @click="currentIndex = (currentIndex - 1 + total) % total"
                                                            class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-black/40 text-white opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-all duration-200 hover:bg-black/60 backdrop-blur-sm hover:scale-110">
                                                            <i class="bi bi-chevron-left"></i>
                                                        </button>
                                                        <button @click="currentIndex = (currentIndex + 1) % total"
                                                            class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-black/40 text-white opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-all duration-200 hover:bg-black/60 backdrop-blur-sm hover:scale-110">
                                                            <i class="bi bi-chevron-right"></i>
                                                        </button>

                                                        {{-- Indicator Dots --}}
                                                        <div class="absolute bottom-2 left-0 right-0 flex justify-center gap-1.5">
                                                            @foreach ($productPhotos as $index => $photo)
                                                                <button @click="currentIndex = {{ $index }}"
                                                                    :class="currentIndex === {{ $index }} ? 'bg-white w-4' : 'bg-white/50 w-1.5'"
                                                                    class="h-1.5 rounded-full transition-all duration-300 shadow-sm">
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                    <i class="bi bi-image text-5xl text-gray-400"></i>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Product Info --}}
                                        <div class="p-4">
                                            <div class="flex items-start justify-between mb-2">
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-900 mb-1">{{ $product->name }}</h4>
                                                    <p class="text-xs text-gray-500 mb-2">
                                                        <i class="bi bi-tag me-1"></i>
                                                        {{ $product->productCategory?->name ?? 'Uncategorized' }}
                                                    </p>
                                                </div>
                                                <span class="text-orange-600 font-bold text-lg">
                                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                                {{ $product->description }}</p>

                                            {{-- Content padding adjustment to make it look balanced without the button --}}
                                            <div class="pt-2"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-16 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                                    <i class="bi bi-box-seam text-2xl text-gray-400"></i>
                                </div>
                                <h4 class="text-base font-bold text-gray-900 mb-1">No products yet</h4>
                                @auth
                                    @if ($canManageBusiness)
                                        <p class="text-sm text-gray-400 mb-5 max-w-xs mx-auto">Start by setting up your product categories, then add your first product.</p>
                                        <div class="flex items-center justify-center gap-3 flex-wrap">
                                            @if ($hasProductCategoriesRoute)
                                                <a href="{{ route('businesses.product-categories.index', $business) }}"
                                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white border border-uco-orange-200 text-uco-orange-600 text-sm font-semibold rounded-xl hover:bg-uco-orange-50 transition-all">
                                                    <i class="bi bi-tags"></i>
                                                    Manage Categories
                                                </a>
                                            @endif
                                            @if ($hasProductsCreateRoute)
                                                <a href="{{ route('businesses.products.create', $business) }}"
                                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-soft-gray-900 hover:bg-soft-gray-800 text-white text-sm font-semibold rounded-xl shadow-sm transition duration-150">
                                                    <i class="bi bi-plus-lg"></i>
                                                    Add Your First Product
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-400">No products have been added yet.</p>
                                    @endif
                                @else
                                    <p class="text-sm text-gray-400">No products have been added yet.</p>
                                @endauth
                            </div>
                        @endif
                    </div>
                    @endif

                {{-- Tab: Services --}}
                @if ($isServiceMode || $isBothMode)
                    <div x-show="activeTab === 'services'" class="p-6" style="display: none;">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Services</h3>
                            @auth
                                @if ($canManageBusiness && $businessServices->count() > 0 && $hasServicesCreateRoute)
                                    <a href="{{ route('businesses.services.create', $business) }}"
                                        class="inline-flex items-center px-3 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-lg shadow-sm transition duration-150">
                                        <i class="bi bi-plus-lg me-2"></i>
                                        Add Service
                                    </a>
                                @endif
                            @endauth
                        </div>

                        @if ($businessServices->count() > 0)
                            <div class="space-y-3">
                                @foreach ($businessServices as $service)
                                    <div @if ($hasServicesShowRoute) @click="if(!event.target.closest('button') && !event.target.closest('a')) window.location='{{ route('businesses.services.show', [$business, $service]) }}'" @endif
                                        class="group border border-gray-200 rounded-lg p-5 hover:bg-gray-50 hover:shadow-md transition-all duration-300 {{ $hasServicesShowRoute ? 'cursor-pointer' : '' }} bg-white">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900 mb-1">{{ $service->name }}</h4>
                                                <p class="text-sm text-gray-600 mb-3">{{ $service->description }}</p>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-orange-600 font-bold">Rp
                                                        {{ number_format($service->price, 0, ',', '.') }}</span>
                                                    <span class="text-xs text-gray-500">/
                                                        {{ $service->price_type }}</span>
                                                </div>
                                            </div>

                                            {{-- Action Buttons --}}
                                            @auth
                                                @if ($canManageBusiness && ($hasServicesEditRoute || $hasServicesDestroyRoute))
                                                    <div class="flex items-center gap-2 ml-4">
                                                        @if ($hasServicesEditRoute)
                                                            <a href="{{ route('businesses.services.edit', [$business, $service]) }}"
                                                                class="inline-flex items-center justify-center w-8 h-8 bg-orange-50 text-orange-600 rounded hover:bg-orange-100 transition duration-150"
                                                                title="Edit Service">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                        @endif
                                                        @if ($hasServicesDestroyRoute)
                                                            <form action="{{ route('businesses.services.destroy', [$business, $service]) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Delete {{ $service->name }}?');"
                                                                class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="inline-flex items-center justify-center w-8 h-8 bg-red-50 text-red-600 rounded hover:bg-red-100 transition duration-150"
                                                                    title="Delete Service">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endauth
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12 bg-gray-50 rounded-lg">
                                <i class="bi bi-wrench text-6xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500 text-lg font-medium mb-2">No services yet</p>
                                @auth
                                    @if ($canManageBusiness && $hasServicesCreateRoute)
                                        <p class="text-sm text-gray-400 mb-4">Add services to showcase what you offer</p>
                                        <a href="{{ route('businesses.services.create', $business) }}"
                                            class="inline-flex items-center px-4 py-2 bg-soft-gray-900 hover:bg-soft-gray-800 text-white text-sm font-medium rounded-xl shadow-sm transition duration-150">
                                            <i class="bi bi-plus-lg me-2"></i>
                                            Add Your First Service
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Tab: Gallery (previously Photos) --}}
                @if ($hasGallery)
                <div x-show="activeTab === 'photos'" class="p-6" style="display: none;">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-bold text-gray-900">Business Gallery</h3>
                        @auth
                            @if ($canManageBusiness && $businessPhotos->count() > 0 && ($hasPhotosIndexRoute || $hasPhotosCreateRoute))
                                <div class="flex items-center gap-3">
                                    @if ($hasPhotosIndexRoute)
                                        <a href="{{ route('businesses.photos.index', $business) }}"
                                            class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all shadow-sm">
                                            <i class="bi bi-grid-3x3-gap me-2"></i>
                                            Manage Gallery
                                        </a>
                                    @endif
                                    @if ($hasPhotosCreateRoute)
                                        <a href="{{ route('businesses.photos.create', $business) }}"
                                            class="inline-flex items-center px-4 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all">
                                            <i class="bi bi-plus-lg me-2"></i>
                                            Upload to Gallery
                                        </a>
                                    @endif
                                </div>
                            @endif
                        @endauth
                    </div>

                    @if ($businessPhotos->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach ($businessPhotos as $photo)
                                <div
                                    class="bg-white rounded-lg overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition-all group flex flex-col">
                                    {{-- Photo --}}
                                    <div class="relative aspect-video overflow-hidden bg-gray-100 flex-shrink-0">
                                        <img src="{{ storage_image_url($photo->photo_url, 'gallery') }}"
                                            alt="{{ $business->name }} gallery photo" loading="lazy"
                                            class="w-full h-full object-cover transition duration-500 group-hover:scale-105 cursor-zoom-in"
                                            @click="openFullscreen('{{ storage_image_url($photo->photo_url, 'gallery') }}', '{{ addslashes($business->name) }} photo')">
                                    </div>

                                    {{-- Caption Section --}}
                                    <div
                                        class="p-4 flex-grow flex flex-col justify-center border-t border-gray-50 bg-white">
                                        @if ($photo->caption)
                                            <p class="text-gray-700 text-sm font-medium leading-normal line-clamp-3">
                                                {{ $photo->caption }}
                                            </p>
                                        @else
                                            <span
                                                class="text-gray-300 text-[10px] uppercase font-bold tracking-widest text-center">No
                                                caption</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-20 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                            <div
                                class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                <i class="bi bi-images text-3xl text-gray-300"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 mb-2">No photos in gallery yet</h4>
                            @auth
                                @if ($canManageBusiness && $hasPhotosCreateRoute)
                                    <p class="text-gray-400 mb-6 max-w-xs mx-auto text-xs">Share your business journey by
                                        uploading photos to your gallery.</p>
                                    <a href="{{ route('businesses.photos.create', $business) }}"
                                        class="inline-flex items-center px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-bold rounded-xl shadow-md transition-all">
                                        <i class="bi bi-upload me-2"></i>
                                        Add First Photo
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
                @endif

                {{-- Tab: Contacts --}}
                @if ($hasContacts)
                <div x-show="activeTab === 'contacts'" class="p-6" style="display: none;">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Contact Information</h3>
                        @auth
                                @if ($canManageBusiness && $businessContacts->count() > 0 && $hasContactsCreateRoute)
                                <a href="{{ route('businesses.contacts.create', $business) }}"
                                    class="inline-flex items-center px-3 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-lg shadow-sm transition duration-150">
                                    <i class="bi bi-plus-lg me-2"></i>
                                    Add Contact
                                </a>
                            @endif
                        @endauth
                    </div>

                    @if ($businessContacts->count() > 0)
                        <div class="space-y-3">
                            @foreach ($businessContacts as $contact)
                                <div
                                    class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150">
                                    <div
                                        class="flex items-center justify-center w-12 h-12 rounded-full bg-orange-100 text-orange-600 flex-shrink-0">
                                        <i class="bi {{ $contact->contactType->icon_class }} text-xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $contact->contactType->platform_name }}</p>
                                        <p class="text-sm text-gray-600 truncate">{{ $contact->contact_value }}</p>
                                    </div>
                                    @if ($contact->is_primary)
                                        <span
                                            class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded whitespace-nowrap">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Primary
                                        </span>
                                    @endif

                                    {{-- Action Buttons --}}
                                    @auth
                                        @if ($canManageBusiness && ($hasContactsEditRoute || $hasContactsDestroyRoute))
                                            <div class="flex items-center gap-2">
                                                @if ($hasContactsEditRoute)
                                                    <a href="{{ route('businesses.contacts.edit', [$business, $contact]) }}"
                                                        class="inline-flex items-center justify-center w-8 h-8 bg-orange-50 text-orange-600 rounded hover:bg-orange-100 transition duration-150"
                                                        title="Edit Contact">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endif
                                                @if ($hasContactsDestroyRoute)
                                                    <form
                                                        action="{{ route('businesses.contacts.destroy', [$business, $contact]) }}"
                                                        method="POST" onsubmit="return confirm('Delete this contact?');"
                                                        class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="inline-flex items-center justify-center w-8 h-8 bg-red-50 text-red-600 rounded hover:bg-red-100 transition duration-150"
                                                            title="Delete Contact">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    @endauth
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <i class="bi bi-telephone text-6xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 text-lg font-medium mb-2">No contact information yet</p>
                            @auth
                                @if ($canManageBusiness && $hasContactsCreateRoute)
                                    <p class="text-sm text-gray-400 mb-4">Add contact methods so customers can reach you
                                    </p>
                                    <a href="{{ route('businesses.contacts.create', $business) }}"
                                        class="inline-flex items-center px-4 py-2 bg-soft-gray-900 hover:bg-soft-gray-800 text-white text-sm font-medium rounded-xl shadow-sm transition duration-150">
                                        <i class="bi bi-plus-lg me-2"></i>
                                        Add Your First Contact
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
                @endif
                @endif

                @if ($visibleSectionCount === 0)
                    <div class="p-6">
                        <div class="text-center py-14 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                <i class="bi bi-inbox text-3xl text-gray-300"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 mb-2">Nothing to show yet</h4>
                            <p class="text-sm text-gray-500 max-w-md mx-auto">
                                This business doesn’t have products, gallery photos, or contact entries yet.
                            </p>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        {{-- Fullscreen Photo Modal --}}
        <div x-show="fullscreenOpen" x-cloak class="fixed inset-0 z-[110]">
            <div class="absolute inset-0 bg-black/90" @click="fullscreenOpen = false"></div>
            <button type="button" @click="fullscreenOpen = false"
                class="absolute top-4 right-4 text-white bg-white/10 hover:bg-white/20 rounded-lg px-3 py-2 z-10">
                <i class="bi bi-x-lg"></i>
            </button>
            <div class="absolute inset-0 flex items-center justify-center p-4 cursor-zoom-out"
                @click="fullscreenOpen = false">
                <img :src="fullscreenSrc" :alt="fullscreenAlt" @click.stop
                    class="max-h-[92vh] max-w-[95vw] object-contain rounded-lg shadow-2xl cursor-default">
            </div>
        </div>

        {{-- User Profile Modal --}}
        <div x-show="showUserModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay with blur --}}
                <div x-show="showUserModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="showUserModal = false"
                    class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Modal panel --}}
                <div x-show="showUserModal" x-transition:enter="ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200 transform"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                    class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-2xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100">

                    {{-- Modal Header & Avatar --}}
                    <div
                        class="relative bg-gradient-to-br from-soft-gray-50 to-white px-6 pt-8 pb-6 border-b border-gray-100">
                        <button type="button" @click="showUserModal = false"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-xl transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        <div class="flex flex-col items-center">
                            @if ($business->user->profile_photo_url)
                                <img src="{{ storage_image_url($business->user->profile_photo_url, 'profile_thumb') }}"
                                    alt="{{ $business->user->name }}"
                                    class="w-24 h-24 rounded-lg object-cover shadow-lg mb-4 border-2 border-white">
                            @else
                                <div
                                    class="w-24 h-24 rounded-lg bg-gradient-to-br from-uco-orange-500 to-uco-yellow-500 flex items-center justify-center text-white text-3xl font-bold shadow-lg mb-4 border-2 border-white">
                                    {{ strtoupper(substr($business->user->name, 0, 1)) }}
                                </div>
                            @endif
                            <h3 class="text-xl font-bold text-gray-900">{{ $business->user->name }}</h3>
                            <p class="text-sm font-medium text-gray-500 mt-1">
                                @if ($business->user->role === 'student')
                                    Student
                                @elseif($business->user->role === 'alumni')
                                    Alumni
                                @elseif($business->user->role === 'admin')
                                    Administrator
                                @else
                                    User
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Modal Body: Tabbed Content --}}
                    <div x-data="{ userTab: 'basic' }" class="bg-gray-50 rounded-b-2xl">
                        <div class="flex border-b border-gray-200">
                            <button @click="userTab = 'basic'"
                                :class="userTab === 'basic' ? 'border-soft-gray-900 text-soft-gray-900 bg-white' :
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-100/50'"
                                class="flex-1 py-3 px-4 border-b-2 font-semibold text-sm transition-colors text-center">
                                Basic Info
                            </button>
                            <button @click="userTab = 'personal'"
                                :class="userTab === 'personal' ? 'border-soft-gray-900 text-soft-gray-900 bg-white' :
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-100/50'"
                                class="flex-1 py-3 px-4 border-b-2 font-semibold text-sm transition-colors text-center">
                                Personal
                            </button>
                            <button @click="userTab = 'academic'"
                                :class="userTab === 'academic' ? 'border-soft-gray-900 text-soft-gray-900 bg-white' :
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-100/50'"
                                class="flex-1 py-3 px-4 border-b-2 font-semibold text-sm transition-colors text-center">
                                Academic
                            </button>
                        </div>

                        <div class="p-6">
                            {{-- Basic Tab --}}
                            <div x-show="userTab === 'basic'" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform translate-y-2"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                class="space-y-4 text-left">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                                            Username</p>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ '@' . $business->user->username }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                                            Full Name</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $business->user->name }}</p>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Email
                                        Address</p>
                                    <a href="mailto:{{ $business->user->email }}"
                                        class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors bg-blue-50 px-3 py-1.5 rounded-lg hover:bg-blue-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        {{ $business->user->email }}
                                    </a>
                                </div>

                                <div class="grid grid-cols-2 gap-4 pt-2">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                                            Phone/Mobile</p>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $business->user->mobile_number ?? ($business->user->phone_number ?? '-') }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                                            WhatsApp</p>
                                        @if ($business->user->whatsapp)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business->user->whatsapp) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1.5 text-sm font-medium text-green-600 hover:text-green-800 transition-colors">
                                                <i class="bi bi-whatsapp"></i> {{ $business->user->whatsapp }}
                                            </a>
                                        @else
                                            <p class="text-sm font-medium text-gray-900">-</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-gray-200">
                                    <p class="text-xs text-gray-400 mt-2">Joined
                                        {{ $business->user->created_at->format('M Y') }}</p>
                                </div>
                            </div>

                            {{-- Personal Tab --}}
                            <div x-show="userTab === 'personal'" x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform translate-y-2"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                class="space-y-4 text-left">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                                            Birth City</p>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $business->user->birth_city ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                                            Birth Date</p>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $business->user->birth_date ? $business->user->birth_date->format('d M Y') : '-' }}
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                                        Religion</p>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $business->user->religion ?? '-' }}</p>
                                </div>

                                @if ($business->user->bio ?? false)
                                    <div class="pt-2">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                                            Bio</p>
                                        <p class="text-sm text-gray-700 leading-relaxed">{{ $business->user->bio }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                            {{-- Academic Tab --}}
                            <div x-show="userTab === 'academic'" x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform translate-y-2"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                class="space-y-4 text-left">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                                            NIS (Student ID)</p>
                                        <p class="text-sm font-mono font-medium text-gray-900">
                                            {{ $business->user->NIS ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                                            Class/Year</p>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $business->user->Student_Year ?? '-' }}</p>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                                        Major/Study Program</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $business->user->Major ?? '-' }}
                                    </p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                                            CGPA</p>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $business->user->CGPA ? number_format($business->user->CGPA, 2) : '-' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                                            Status</p>
                                        @if ($business->user->Is_Graduate)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-md bg-green-100 text-green-800">
                                                <i class="bi bi-mortarboard-fill"></i> Graduated
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-md bg-blue-100 text-blue-800">
                                                <i class="bi bi-book-half"></i> Active Student
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </div>
        {{-- END LEFT COLUMN --}}

        {{-- ═══ RIGHT COLUMN (35%) ═══ --}}
        <div class="md:sticky md:top-6 min-w-0 flex flex-col gap-6">

            @php
                $owner      = $business->user;
                $ownerPhoto = $owner->profile_photo_url;
                $ownerAcad  = $owner->academic_data ?? [];
                $ownerGrad  = $owner->graduation_data ?? [];
                $ownerPerso = $owner->personal_data ?? [];
                $ownerRoleLabel = match ($owner->role ?? '') {
                    'admin'   => 'Administrator',
                    'alumni'  => 'UCO Alumni',
                    'student' => 'UCO Student',
                    default   => ucfirst($owner->role ?? 'Member'),
                };
                $ownerPhotoUrl = $ownerPhoto
                    ? storage_image_url($ownerPhoto, ['width' => 300, 'height' => 300, 'crop' => 'thumb', 'quality' => 'auto', 'fetch_format' => 'auto'])
                    : null;
                $additionalOwners = $business->members()->where('users.id', '!=', $business->user_id)->get();
            @endphp

            {{-- ✨ Elegant Owner Card (Clickable) --}}
            <a href="{{ route('users.show', $owner) }}" class="block relative bg-white rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden group hover:border-orange-200 hover:shadow-[0_8px_30px_rgb(247,147,30,0.1)] transition-all">
                
                {{-- Decorative gradient blob --}}
                <div class="absolute -top-12 -right-12 w-32 h-32 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full blur-[40px] opacity-10 group-hover:opacity-20 transition-opacity"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.15em]">
                            {{ strtolower($owner->current_status ?? '') === 'entrepreneur' ? 'Owned By' : 'Main Contact' }}
                        </p>
                        <i class="bi bi-box-arrow-up-right text-gray-300 group-hover:text-orange-500 transition-colors text-xs"></i>
                    </div>

                    <div class="flex items-center gap-4 mb-5">
                        {{-- Avatar --}}
                        @if ($ownerPhotoUrl)
                            <img src="{{ $ownerPhotoUrl }}" alt="{{ $owner->name }}"
                                class="w-14 h-14 rounded-xl object-cover shadow-sm ring-1 ring-black/5 flex-shrink-0">
                        @else
                            <div class="w-14 h-14 rounded-xl flex items-center justify-center shadow-sm ring-1 ring-black/5 bg-gradient-to-br from-gray-50 to-gray-100 flex-shrink-0">
                                <span class="text-xl font-black text-gray-400 select-none">
                                    {{ strtoupper(substr($owner->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif

                        {{-- Name & Role --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-extrabold text-gray-900 leading-tight truncate group-hover:text-orange-600 transition-colors">
                                {{ $owner->name }}
                            </h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-gray-100 text-gray-600">
                                    {{ $ownerRoleLabel }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if ($business->position)
                        <div class="mb-4 flex items-center gap-2 text-xs font-medium text-gray-500 bg-gray-50 rounded-lg p-2.5 border border-gray-100">
                            <i class="bi bi-briefcase text-gray-400"></i>
                            <span class="truncate">{{ $business->position }}</span>
                        </div>
                    @endif

                    {{-- Contact Data Inside Card --}}
                    <div class="space-y-2 mt-4 pt-4 border-t border-gray-50">
                        @if ($owner->whatsapp)
                            <div class="flex items-center gap-2.5 text-xs">
                                <div class="w-6 h-6 rounded flex items-center justify-center bg-green-50 text-green-600 flex-shrink-0">
                                    <i class="bi bi-whatsapp"></i>
                                </div>
                                <span class="text-gray-600 font-medium truncate">{{ $owner->whatsapp }}</span>
                            </div>
                        @endif
                        @if ($ownerPerso['instagram'] ?? false)
                            <div class="flex items-center gap-2.5 text-xs">
                                <div class="w-6 h-6 rounded flex items-center justify-center bg-pink-50 text-pink-600 flex-shrink-0">
                                    <i class="bi bi-instagram"></i>
                                </div>
                                <span class="text-gray-600 font-medium truncate">{{ $ownerPerso['instagram'] }}</span>
                            </div>
                        @endif
                        @if ($ownerGrad['official_email'] ?? false)
                            <div class="flex items-center gap-2.5 text-xs">
                                <div class="w-6 h-6 rounded flex items-center justify-center bg-blue-50 text-blue-600 flex-shrink-0">
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                                <span class="text-gray-600 font-medium truncate">{{ $ownerGrad['official_email'] }}</span>
                            </div>
                        @endif
                        @if(!($owner->whatsapp) && !($ownerPerso['instagram'] ?? false) && !($ownerGrad['official_email'] ?? false))
                             <p class="text-[10px] text-gray-400 italic">Click to view full profile details</p>
                        @endif
                    </div>
                </div>
            </a>

            {{-- Additional Owners --}}
            @if ($additionalOwners->isNotEmpty())
                <div class="mt-8">
                    <p class="text-[10px] font-black uppercase tracking-[0.15em] mb-4 flex items-center gap-2 text-gray-500">
                        <i class="bi bi-people-fill"></i>
                        Also Managed By
                    </p>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach ($additionalOwners as $addOwner)
                            @php
                                $addPhotoUrl = $addOwner->profile_photo_url
                                    ? storage_image_url($addOwner->profile_photo_url, ['width' => 150, 'height' => 150, 'crop' => 'thumb'])
                                    : null;
                            @endphp
                            <a href="{{ route('users.show', $addOwner) }}" class="flex flex-col items-center justify-center gap-2.5 p-4 bg-white rounded-2xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] hover:shadow-[0_8px_30px_rgb(247,147,30,0.08)] hover:border-orange-200 transition-all text-center group">
                                @if ($addPhotoUrl)
                                    <img src="{{ $addPhotoUrl }}" alt="{{ $addOwner->name }}"
                                        class="w-12 h-12 rounded-full object-cover shadow-sm ring-1 ring-black/5 group-hover:scale-105 transition-transform">
                                @else
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-base font-black bg-gradient-to-br from-gray-50 to-gray-100 text-gray-400 ring-1 ring-black/5 group-hover:scale-105 transition-transform shadow-sm group-hover:from-orange-50 group-hover:to-orange-100 group-hover:text-orange-600">
                                        {{ strtoupper(substr($addOwner->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="min-w-0 w-full">
                                    <p class="text-xs font-bold text-gray-900 leading-tight truncate group-hover:text-orange-600 transition-colors">{{ $addOwner->name }}</p>
                                    <p class="text-[10px] font-medium text-gray-500 capitalize truncate mt-0.5">{{ $addOwner->pivot->position ?? ($addOwner->role ?? 'Member') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
        {{-- END RIGHT COLUMN --}}

        </div>
        {{-- END 2-COLUMN GRID --}}

    </div>

    {{-- 65/35 Grid Layout Styles --}}
    <style>
        @media (min-width: 768px) {
            #biz-layout-grid {
                grid-template-columns: 65fr 35fr;
            }
        }
    </style>

    {{-- Smart Scroll Restoration Script --}}

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Restore scroll position after a reload/redirect if stored
            const scrollPos = sessionStorage.getItem('uco_business_scroll_pos');
            if (scrollPos) {
                // Use setTimeout to ensure all dynamic elements are rendered
                setTimeout(() => {
                    window.scrollTo({
                        top: parseInt(scrollPos),
                        behavior: 'instant'
                    });
                    sessionStorage.removeItem('uco_business_scroll_pos');
                }, 50);
            }

            // Store scroll position on form submissions or specific actions
            const saveScrollPos = () => {
                sessionStorage.setItem('uco_business_scroll_pos', window.scrollY);
            };

            // Global listener for forms and action buttons that redirect back
            document.querySelectorAll('form, .btn-save-scroll').forEach(el => {
                el.addEventListener('submit', saveScrollPos);
                el.addEventListener('click', (e) => {
                    if (e.currentTarget.tagName === 'A' || e.currentTarget.tagName === 'BUTTON') {
                        saveScrollPos();
                    }
                });
            });
        });
    </script>
</x-app-layout>
