@use('Illuminate\Support\Facades\Storage')

<x-app-layout>
    @section('title', $business->name . ' - ' . ($business->category->name ?? 'Venture'))
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
        $allProductsAndServices = collect($business->products ?? []);
        $businessProducts = $allProductsAndServices->where('type', 'product');
        $businessPhotos = collect($business->photos ?? []);
        $businessServices = $allProductsAndServices->where('type', 'service');
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
        },
        productModalOpen: false,
        productModalName: '',
        productModalDesc: '',
        productModalPhoto: '',
        productModalPrice: '',
        productModalLabel: '',
        openProductModal(name, desc, photo, price, label) {
            this.productModalName = name;
            this.productModalDesc = desc;
            this.productModalPhoto = photo;
            this.productModalPrice = price;
            this.productModalLabel = label;
            this.productModalOpen = true;
        }
    }" x-init="$watch('showUserModal', val => document.body.style.overflow = val ? 'hidden' : '');
    $watch('fullscreenOpen', val => { if (val) { document.body.style.overflow = 'hidden'; } else if (!showUserModal && !productModalOpen) { document.body.style.overflow = ''; } });
    $watch('productModalOpen', val => { if (val) { document.body.style.overflow = 'hidden'; } else if (!showUserModal && !fullscreenOpen) { document.body.style.overflow = ''; } })">
        {{-- Hero Section with Elegant Back Button --}}
        <div class="mb-8 px-4 sm:px-0">
            {{-- Breadcrumbs --}}
            <nav class="flex pt-4 sm:pt-6 mb-8 text-sm font-medium" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('businesses.index') }}?view=entrepreneur" class="text-gray-400 hover:text-uco-orange-500 transition">Directory</a></li>
                    <li class="flex items-center space-x-2">
                        <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
                        <span class="text-gray-900">{{ $business->name }}</span>
                    </li>
                </ol>
            </nav>

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
                            class="btn-uco btn-uco-secondary">
                            <i class="bi bi-pencil-square"></i>
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
                @php $heroPhotosCount = $businessPhotos->count(); @endphp
                @if ($heroPhotosCount > 0)
                <div class="relative h-64 sm:h-72 lg:h-80 overflow-hidden group"
                    x-data="{
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

                    @foreach($businessPhotos as $index => $photo)
                        <div x-show="activeHeroSlide === {{ $index }}"
                            x-transition:enter="transition ease-out duration-1000"
                            x-transition:enter-start="opacity-0 scale-105"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-1000" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0" class="absolute inset-0 w-full h-full">
                            <img src="{{ storage_image_url($photo->photo_url, 'hero') }}"
                                alt="{{ $business->name }} - Hero Photo {{ $index + 1 }}"
                                class="w-full h-full object-cover cursor-zoom-in transition-transform duration-500 hover:scale-105"
                                @click="openFullscreen('{{ storage_image_url($photo->photo_url, 'hero') }}', '{{ addslashes($business->name) }} - Hero Photo {{ $index + 1 }}')">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent pointer-events-none">
                            </div>
                        </div>
                    @endforeach

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
                @endif


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
                    @php
                        $showRevenue = Gate::allows('update', $business);
                    @endphp
                    <div class="grid grid-cols-1 {{ $showRevenue ? 'sm:grid-cols-2' : 'sm:grid-cols-3' }} gap-y-6 gap-x-4 md:gap-x-6 lg:gap-x-8 py-8 mb-4 border-y border-gray-100">
                        {{-- Revenue (SENSITIVE - PROTECTED) --}}
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

                        {{-- Employee Count --}}
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

                        {{-- Venture Inception --}}
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

                        {{-- Academic Heritage --}}
                        <div class="flex items-start gap-4 sm:gap-2 md:gap-4">
                            <div class="w-11 h-11 sm:w-9 sm:h-9 md:w-11 md:h-11 rounded-lg bg-orange-50 text-uco-orange-600 flex items-center justify-center border border-orange-100/50 shadow-sm flex-shrink-0">
                                <i class="bi bi-mortarboard-fill text-lg sm:text-sm md:text-lg"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1.5">Academic Heritage</p>
                                <h5 class="text-sm sm:text-xs md:text-sm lg:text-base font-extrabold text-gray-800">{{ $business->academic_heritage ?? 'UCO Legacy' }}</h5>
                                <p class="text-[9px] text-gray-400 font-medium mt-0.5">Founding academic batch</p>
                            </div>
                        </div>
                    </div>

                    {{-- Comprehensive Business Details --}}
                    <div class="mb-12">
                        <h4 class="text-sm font-bold text-soft-gray-900 uppercase tracking-wider mb-6">Business Operations & Details</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Target Market --}}
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Target Market</p>
                                <p class="text-sm font-normal text-gray-800">{{ $business->target_market ?: 'Not specified' }}</p>
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
                                        @php
                                            $igHandles = array_filter(array_map('trim', preg_split('/[,;\s]+/', $business->instagram)));
                                        @endphp
                                        @foreach($igHandles as $handle)
                                            @php
                                                $cleanHandle = ltrim($handle, '@');
                                            @endphp
                                            @if(!empty($cleanHandle))
                                                <a href="https://instagram.com/{{ $cleanHandle }}" target="_blank" class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-pink-50 hover:bg-pink-100 text-pink-700 transition-colors border border-pink-200">
                                                    <i class="bi bi-instagram"></i>
                                                    <span class="text-xs font-bold">@ {{ $cleanHandle }}</span>
                                                </a>
                                            @endif
                                        @endforeach
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
            
            {{-- ═══ FLOWING SECTIONS: Products → Services → Gallery → Contacts ═══ --}}
            <div class="mt-4 space-y-6">

                {{-- ── PRODUCTS SECTION ── --}}
                @if (($isProductMode || $isBothMode) && ($hasProducts || $canManageBusiness))
                    <div class="bg-white shadow-lg sm:rounded-lg border border-soft-gray-100 overflow-hidden">
                        {{-- Section Header --}}
                        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-1 h-8 bg-uco-orange-500 rounded-full"></div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-widest text-uco-orange-500 mb-0.5">What We Offer</p>
                                    <h2 class="text-xl font-bold text-gray-900">Our Products</h2>
                                </div>
                            </div>
                            @auth
                                @if ($canManageBusiness)
                                    <div class="flex items-center gap-2">
                                        @if ($hasProductCategoriesRoute)
                                            <a href="{{ route('businesses.product-categories.index', $business) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-uco-orange-50 hover:bg-uco-orange-100 border border-uco-orange-200 text-uco-orange-600 text-sm font-semibold rounded-lg transition">
                                                <i class="bi bi-tags"></i>
                                                Categories
                                            </a>
                                        @endif
                                        @if ($hasProductsCreateRoute)
                                            <a href="{{ route('businesses.products.create', $business) }}"
                                                class="btn-uco btn-uco-primary">
                                                <i class="bi bi-plus-lg"></i>
                                                Add Product
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            @endauth
                        </div>

                        {{-- Products Grid --}}
                        <div class="p-6">
                            @if ($businessProducts->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($businessProducts as $product)
                                        @php
                                            $formattedPrice = '';
                                            $formattedLabel = '';
                                            if (is_numeric($product->price) && $product->price_type !== 'unspecified' && $product->price_type !== 'customize') {
                                                $formattedPrice = 'Rp ' . number_format((float) $product->price, 0, ',', '.');
                                            } elseif ($product->price_type === 'customize') {
                                                $formattedPrice = 'Customize by Order';
                                            } else {
                                                $formattedPrice = 'Price Unspecified';
                                            }

                                            if (is_numeric($product->price) && in_array($product->price_type, ['fixed', 'negotiable'])) {
                                                $formattedLabel = $product->price_type === 'fixed' ? 'Fixed Price' : 'Negotiable';
                                            }
                                        @endphp
                                        <div @if($product->getRawOriginal('photo_url')) 
                                                @click='openProductModal(@json($product->name), @json($product->description), @json($product->photo_url), @json($formattedPrice), @json($formattedLabel))'
                                             @endif
                                            class="group relative flex flex-col h-full border border-gray-200 rounded-2xl overflow-hidden hover:shadow-xl hover:border-orange-200 transition-all duration-300 {{ $product->getRawOriginal('photo_url') ? 'cursor-pointer' : '' }} bg-white">
                                            
                                            {{-- Manager Actions Overlay --}}
                                            @auth
                                                @if ($canManageBusiness)
                                                    <div class="absolute top-3 right-3 flex items-center gap-1.5 z-10" @click.stop>
                                                        <a href="{{ route('businesses.products.edit', [$business, $product]) }}"
                                                            class="inline-flex items-center justify-center w-8 h-8 bg-white/95 hover:bg-white text-gray-700 hover:text-uco-orange-600 rounded-full shadow-md transition-all duration-300 border border-gray-100"
                                                            title="Edit Product">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>
                                                        <form action="{{ route('businesses.products.destroy', [$business, $product]) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this product?');"
                                                            class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="inline-flex items-center justify-center w-8 h-8 bg-white/95 hover:bg-red-50 text-red-600 hover:text-red-700 rounded-full shadow-md transition-all duration-300 border border-gray-100"
                                                                title="Delete Product">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endauth

                                            {{-- Product Image --}}
                                            <div class="relative aspect-square w-full bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                                                @if ($product->getRawOriginal('photo_url'))
                                                    <img src="{{ $product->photo_url }}"
                                                        alt="{{ $product->name }}"
                                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                                @else
                                                    <i class="bi bi-image text-5xl text-gray-300"></i>
                                                @endif
                                            </div>

                                            {{-- Product Info --}}
                                            <div class="pt-4 px-4 pb-3 flex flex-col flex-grow">
                                                {{-- Name --}}
                                                <h4 class="font-bold text-gray-800 text-sm line-clamp-2 leading-snug mb-1.5 group-hover:text-uco-orange-600 transition-colors" title="{{ $product->name }}">
                                                    {{ $product->name }}
                                                </h4>

                                                {{-- Description --}}
                                                <p class="text-xs text-gray-500 line-clamp-2 mb-3 leading-relaxed">
                                                    {{ $product->description }}
                                                </p>

                                                {{-- Price & Price Type --}}
                                                <div class="mt-auto flex flex-col items-start gap-1">
                                                    <div>
                                                        @if(is_numeric($product->price) && $product->price_type !== 'unspecified' && $product->price_type !== 'customize')
                                                            <span class="text-base font-extrabold text-[#f7931e]">
                                                                Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                                                            </span>
                                                        @elseif($product->price_type === 'customize')
                                                            <span class="inline-flex items-center text-xs font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                                                Customize by Order
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center text-xs font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                                                Price Unspecified
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if(is_numeric($product->price) && in_array($product->price_type, ['fixed', 'negotiable']))
                                                        <span class="inline-flex items-center text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded uppercase tracking-wider">
                                                            @if($product->price_type === 'fixed')
                                                                Fixed Price
                                                            @elseif($product->price_type === 'negotiable')
                                                                Negotiable
                                                            @endif
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex items-center justify-center py-6 px-4 bg-gray-50/50 rounded-lg border border-dashed border-gray-200">
                                    <span class="text-sm text-gray-500">No products added yet. Start building your product catalog.</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- ── SERVICES SECTION ── --}}
                @if (($isServiceMode || $isBothMode) && ($businessServices->count() > 0 || $canManageBusiness))
                    <div class="bg-white shadow-lg sm:rounded-lg border border-soft-gray-100 overflow-hidden">
                        {{-- Section Header --}}
                        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-1 h-8 bg-uco-orange-500 rounded-full"></div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-widest text-uco-orange-500 mb-0.5">Expert Solutions</p>
                                    <h2 class="text-xl font-bold text-gray-900">Our Services</h2>
                                </div>
                            </div>
                            @auth
                                @if ($canManageBusiness && $hasServicesCreateRoute)
                                    <a href="{{ route('businesses.services.create', $business) }}"
                                        class="btn-uco btn-uco-primary">
                                        <i class="bi bi-plus-lg"></i>
                                        Add Service
                                    </a>
                                @endif
                            @endauth
                        </div>

                        {{-- Services Grid --}}
                        <div class="p-6">
                            @if ($businessServices->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($businessServices as $service)
                                        @php
                                            $formattedPrice = '';
                                            $formattedLabel = '';
                                            if (is_numeric($service->price) && $service->price_type !== 'unspecified' && $service->price_type !== 'customize') {
                                                $formattedPrice = 'Rp ' . number_format((float) $service->price, 0, ',', '.');
                                            } elseif ($service->price_type === 'customize') {
                                                $formattedPrice = 'Customize by Order';
                                            } else {
                                                $formattedPrice = 'Price Unspecified';
                                            }

                                            if (is_numeric($service->price) && in_array($service->price_type, ['fixed', 'negotiable'])) {
                                                $formattedLabel = $service->price_type === 'fixed' ? 'Fixed Price' : 'Negotiable';
                                            }
                                        @endphp
                                        <div @if($service->getRawOriginal('photo_url')) 
                                                @click='openProductModal(@json($service->name), @json($service->description), @json($service->photo_url), @json($formattedPrice), @json($formattedLabel))'
                                             @endif
                                            class="group relative flex flex-col h-full border border-gray-200 rounded-2xl overflow-hidden hover:shadow-xl hover:border-orange-200 transition-all duration-300 {{ $service->getRawOriginal('photo_url') ? 'cursor-pointer' : '' }} bg-white">
                                            
                                            {{-- Manager Actions Overlay --}}
                                            @auth
                                                @if ($canManageBusiness)
                                                    <div class="absolute top-3 right-3 flex items-center gap-1.5 z-10" @click.stop>
                                                        <a href="{{ route('businesses.services.edit', [$business, $service]) }}"
                                                            class="inline-flex items-center justify-center w-8 h-8 bg-white/95 hover:bg-white text-gray-700 hover:text-uco-orange-600 rounded-full shadow-md transition-all duration-300 border border-gray-100"
                                                            title="Edit Service">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>
                                                        <form action="{{ route('businesses.services.destroy', [$business, $service]) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this service?');"
                                                            class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="inline-flex items-center justify-center w-8 h-8 bg-white/95 hover:bg-red-50 text-red-600 hover:text-red-700 rounded-full shadow-md transition-all duration-300 border border-gray-100"
                                                                title="Delete Service">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endauth

                                            {{-- Service Image --}}
                                            <div class="relative aspect-square w-full bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                                                @if ($service->getRawOriginal('photo_url'))
                                                    <img src="{{ $service->photo_url }}"
                                                        alt="{{ $service->name }}"
                                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                                @else
                                                    <i class="bi bi-image text-5xl text-gray-300"></i>
                                                @endif
                                            </div>

                                            {{-- Service Info --}}
                                            <div class="pt-4 px-4 pb-3 flex flex-col flex-grow">
                                                {{-- Name --}}
                                                <h4 class="font-bold text-gray-800 text-sm line-clamp-2 leading-snug mb-1.5 group-hover:text-uco-orange-600 transition-colors" title="{{ $service->name }}">
                                                    {{ $service->name }}
                                                    </h4>

                                                {{-- Description --}}
                                                <p class="text-xs text-gray-500 line-clamp-2 mb-3 leading-relaxed">
                                                    {{ $service->description }}
                                                </p>

                                                {{-- Price & Price Type --}}
                                                <div class="mt-auto flex flex-col items-start gap-1">
                                                    <div>
                                                        @if(is_numeric($service->price) && $service->price_type !== 'unspecified' && $service->price_type !== 'customize')
                                                            <span class="text-base font-extrabold text-[#f7931e]">
                                                                Rp {{ number_format((float) $service->price, 0, ',', '.') }}
                                                            </span>
                                                        @elseif($service->price_type === 'customize')
                                                            <span class="inline-flex items-center text-xs font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                                                Customize by Order
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center text-xs font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                                                Price Unspecified
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if(is_numeric($service->price) && in_array($service->price_type, ['fixed', 'negotiable']))
                                                        <span class="inline-flex items-center text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded uppercase tracking-wider">
                                                            @if($service->price_type === 'fixed')
                                                                Fixed Price
                                                            @elseif($service->price_type === 'negotiable')
                                                                Negotiable
                                                            @endif
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex items-center justify-center py-6 px-4 bg-gray-50/50 rounded-lg border border-dashed border-gray-200">
                                    <span class="text-sm text-gray-500">No services added yet. Add your first service to showcase what you offer.</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- ── GALLERY SECTION ── --}}
                @if ($hasGallery)
                    <div class="bg-white shadow-lg sm:rounded-lg border border-soft-gray-100 overflow-hidden">
                        {{-- Section Header --}}
                        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-1 h-8 bg-uco-orange-500 rounded-full"></div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-widest text-uco-orange-500 mb-0.5">Visual Story</p>
                                    <h2 class="text-xl font-bold text-gray-900">Business Gallery</h2>
                                </div>
                            </div>
                            @auth
                                @if ($canManageBusiness && ($hasPhotosIndexRoute || $hasPhotosCreateRoute))
                                    <div class="flex items-center gap-2">
                                        @if ($hasPhotosIndexRoute)
                                            <a href="{{ route('businesses.photos.index', $business) }}"
                                                class="btn-uco btn-uco-secondary">
                                                <i class="bi bi-grid-3x3-gap"></i>
                                                Manage
                                            </a>
                                        @endif
                                        @if ($hasPhotosCreateRoute)
                                            <a href="{{ route('businesses.photos.create', $business) }}"
                                                class="btn-uco btn-uco-primary">
                                                <i class="bi bi-upload"></i>
                                                Upload
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            @endauth
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                @foreach ($businessPhotos as $photo)
                                    <div class="bg-white rounded-lg overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition-all group flex flex-col">
                                        <div class="relative aspect-video overflow-hidden bg-gray-100 flex-shrink-0">
                                            <img src="{{ storage_image_url($photo->photo_url, 'gallery') }}"
                                                alt="{{ $business->name }} gallery photo" loading="lazy"
                                                class="w-full h-full object-cover transition duration-500 group-hover:scale-105 cursor-zoom-in"
                                                @click="openFullscreen('{{ storage_image_url($photo->photo_url, 'gallery') }}', '{{ addslashes($business->name) }} photo')">
                                        </div>
                                        <div class="p-4 flex-grow flex flex-col justify-center border-t border-gray-50 bg-white">
                                            @if ($photo->caption)
                                                <p class="text-gray-700 text-sm font-medium leading-normal line-clamp-3">{{ $photo->caption }}</p>
                                            @else
                                                <span class="text-gray-300 text-[10px] uppercase font-bold tracking-widest text-center">No caption</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ── CONTACTS SECTION ── --}}
                @if ($hasContacts)
                    <div class="bg-white shadow-lg sm:rounded-lg border border-soft-gray-100 overflow-hidden">
                        {{-- Section Header --}}
                        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-1 h-8 bg-uco-orange-500 rounded-full"></div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-widest text-uco-orange-500 mb-0.5">Get In Touch</p>
                                    <h2 class="text-xl font-bold text-gray-900">Contact Information</h2>
                                </div>
                            </div>
                            @auth
                                @if ($canManageBusiness && $hasContactsCreateRoute)
                                    <a href="{{ route('businesses.contacts.create', $business) }}"
                                        class="btn-uco btn-uco-primary">
                                        <i class="bi bi-plus-lg"></i>
                                        Add Contact
                                    </a>
                                @endif
                            @endauth
                        </div>

                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach ($businessContacts as $contact)
                                    <div class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150">
                                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-orange-100 text-orange-600 flex-shrink-0">
                                            <i class="bi {{ $contact->contactType->icon_class }} text-xl"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900">{{ $contact->contactType->platform_name }}</p>
                                            <p class="text-sm text-gray-600 truncate">{{ $contact->contact_value }}</p>
                                        </div>
                                        @if ($contact->is_primary)
                                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded whitespace-nowrap">
                                                <i class="bi bi-check-circle me-1"></i>Primary
                                            </span>
                                        @endif
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
                                                        <form action="{{ route('businesses.contacts.destroy', [$business, $contact]) }}"
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
                        </div>
                    </div>
                @endif

                {{-- Nothing to show at all --}}
                @if ($visibleSectionCount === 0 && !$canManageBusiness)
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

        {{-- Product Details Modal --}}
        <div x-show="productModalOpen" x-cloak class="fixed inset-0 z-[9998] flex items-center justify-center p-4 sm:p-6"
             @click.self="closeProductModal()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
             
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeProductModal()"></div>

            <!-- Modal Content -->
            <div x-show="productModalOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="relative w-full max-w-4xl bg-white rounded-[2rem] sm:rounded-[2.5rem] overflow-hidden shadow-[0_30px_70px_rgba(0,0,0,0.25)] border border-slate-100 z-50 flex flex-col md:flex-row h-auto max-h-[90vh] md:min-h-[400px]">
                 
                <!-- Left Column: Product Image -->
                <div class="md:w-5/12 bg-slate-100 relative flex-shrink-0 min-h-[250px] md:min-h-full group">
                    <template x-if="productModalPhoto">
                        <img :src="productModalPhoto" :alt="productModalName" class="w-full h-full object-cover absolute inset-0 transition-transform duration-700">
                    </template>
                    <template x-if="!productModalPhoto">
                        <div class="w-full h-full absolute inset-0 flex items-center justify-center text-slate-300 bg-slate-100">
                            <i class="bi bi-image text-5xl"></i>
                        </div>
                    </template>
                </div>

                <!-- Right Column: Product Info -->
                <div class="md:w-7/12 p-6 sm:p-8 md:p-10 flex flex-col h-full bg-white relative overflow-y-auto">
                    <!-- Close button -->
                    <button @click="closeProductModal()" type="button" class="absolute top-4 right-4 sm:top-6 sm:right-6 w-10 h-10 flex items-center justify-center rounded-full bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors z-10">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    
                    <div class="pr-8 mb-6">
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 leading-tight mb-4" x-text="productModalName"></h2>
                        <div class="prose prose-sm sm:prose-base text-slate-600 max-w-none" style="white-space: pre-wrap;" x-text="productModalDesc"></div>
                    </div>
                    
                    <div class="mt-auto pt-6 border-t border-slate-100">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Price Details</p>
                            <div class="flex items-center flex-wrap gap-3">
                                <span class="text-2xl sm:text-3xl font-black text-uco-orange-600" x-text="productModalPrice"></span>
                                <template x-if="productModalLabel">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 uppercase tracking-wider" x-text="productModalLabel"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Fullscreen Photo Modal --}}
        <div x-show="fullscreenOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/95 cursor-zoom-out" @click="fullscreenOpen = false"></div>
            <div class="relative max-w-5xl max-h-[90vh] flex items-center justify-center pointer-events-none">
                <img :src="fullscreenSrc" :alt="fullscreenAlt"
                     class="max-w-[95vw] max-h-[92vh] object-contain rounded-lg shadow-2xl pointer-events-auto cursor-zoom-out"
                     @click="fullscreenOpen = false">
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
                $additionalOwners = $business->members()->where('users.is_visible', true)->where('users.id', '!=', $business->user_id)->get();
            @endphp

            {{-- Owner Section (Title & Card) --}}
            <div class="flex flex-col gap-3">
                {{-- Section Title: Owned By --}}
                <div class="relative">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-gradient-to-b from-[#f7931e] to-[#fdb913] rounded-full flex-shrink-0"></span>
                        <h4 class="text-base font-black uppercase tracking-[0.15em] text-gray-700">Owned <span class="uco-text-gradient-orange">By</span></h4>
                    </div>
                </div>

                {{-- ✨ Elegant Owner Card --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden transition hover:shadow-md duration-300 group/card hover:border-orange-200 cursor-pointer">
                    {{-- Invisible link that stretches over the whole card --}}
                    <a href="{{ route('users.show', $owner) }}" class="absolute inset-0 z-10" aria-label="View {{ $owner->name }} Profile"></a>

                    {{-- Arrow Icon (visual only now) --}}
                    <div class="absolute top-4 right-4 text-gray-300 group-hover/card:text-orange-500 transition-colors z-10">
                        <i class="bi bi-box-arrow-up-right text-xs"></i>
                    </div>

                    {{-- Header: Avatar & Name --}}
                    <div class="flex items-center gap-5 relative z-10 pointer-events-none">
                        <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex-shrink-0 bg-gray-50 flex items-center justify-center">
                            @if ($ownerPhotoUrl)
                                <img src="{{ $ownerPhotoUrl }}" alt="{{ $owner->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-3xl font-black opacity-20 select-none">{{ strtoupper(substr($owner->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h2 class="text-lg md:text-xl font-extrabold text-gray-900 leading-tight tracking-tight truncate">{{ $owner->name }}</h2>
                            <p class="text-gray-400 font-bold text-[11px] mt-1 tracking-[0.1em] uppercase">{{ $owner->student_status ?: 'Active' }}</p>
                        </div>
                    </div>

                    <div class="w-full h-px bg-gray-100 my-5"></div>

                    {{-- Academic Details --}}
                    <div class="space-y-3">
                        <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] mb-3">Academic Details</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-[13px] font-medium text-gray-500">Major</span>
                            <span class="text-[13px] font-bold text-gray-900">{{ $owner->major ?: '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[13px] font-medium text-gray-500">Batch</span>
                            <span class="text-[13px] font-bold text-gray-900">{{ $owner->year_of_enrollment ?: '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[13px] font-medium text-gray-500">Focus</span>
                            <span class="text-[13px] font-bold text-gray-900">{{ $owner->current_status ?: ($ownerRoleLabel ?: '-') }}</span>
                        </div>
                    </div>

                    {{-- Contacts / WhatsApp --}}
                    @if($owner->whatsapp || $owner->email || ($ownerPerso['instagram'] ?? false))
                    <div class="w-full h-px bg-gray-100 my-5 relative z-10 pointer-events-none"></div>
                    <div class="space-y-4 relative z-20">
                        @if($owner->whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $owner->whatsapp) }}" target="_blank" class="flex items-center justify-between group cursor-pointer">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-green-50 text-green-500 flex items-center justify-center group-hover:bg-green-100 transition-colors">
                                    <i class="bi bi-whatsapp text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-tight">WhatsApp</p>
                                    <p class="text-sm font-bold text-gray-800 group-hover:text-green-600 transition-colors leading-tight mt-0.5">{{ $owner->whatsapp }}</p>
                                </div>
                            </div>
                            <i class="bi bi-arrow-up-right text-gray-300 group-hover:text-green-500 transition-colors text-sm"></i>
                        </a>
                        @endif

                        @if($owner->email)
                        <a href="mailto:{{ $owner->email }}" class="flex items-center justify-between group cursor-pointer">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                                    <i class="bi bi-envelope text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-tight">Email</p>
                                    <p class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition-colors leading-tight mt-0.5">{{ $owner->email }}</p>
                                </div>
                            </div>
                            <i class="bi bi-arrow-up-right text-gray-300 group-hover:text-blue-500 transition-colors text-sm"></i>
                        </a>
                        @endif

                        @if($ownerPerso['instagram'] ?? false)
                            @php
                                $ownerIgHandles = array_filter(array_map('trim', preg_split('/[,;\s]+/', $ownerPerso['instagram'])));
                            @endphp
                            @foreach($ownerIgHandles as $handle)
                                @php
                                    $cleanHandle = ltrim($handle, '@');
                                @endphp
                                @if(!empty($cleanHandle))
                                    <a href="https://instagram.com/{{ $cleanHandle }}" target="_blank" class="flex items-center justify-between group cursor-pointer mb-2 last:mb-0">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-full bg-pink-50 text-pink-500 flex items-center justify-center group-hover:bg-pink-100 transition-colors">
                                                <i class="bi bi-instagram text-lg"></i>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-tight">Instagram</p>
                                                <p class="text-sm font-bold text-gray-800 group-hover:text-pink-600 transition-colors leading-tight mt-0.5">@ {{ $cleanHandle }}</p>
                                            </div>
                                        </div>
                                        <i class="bi bi-arrow-up-right text-gray-300 group-hover:text-pink-500 transition-colors text-sm"></i>
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            {{-- Additional Owners Section (Title & Grid) --}}
            @if ($additionalOwners->isNotEmpty())
                <div class="flex flex-col gap-3">
                    <div class="relative">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-gradient-to-b from-[#2563eb] to-[#60a5fa] rounded-full flex-shrink-0"></span>
                            <h4 class="text-base font-black uppercase tracking-[0.15em] text-gray-700">Also Managed <span class="uco-text-gradient-blue">By</span></h4>
                        </div>
                    </div>
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
