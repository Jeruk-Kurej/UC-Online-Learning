<x-app-layout>
    @section('title', $business->name . ' - ' . ($business->category->name ?? 'Business'))
    
    @push('meta')
        <meta name="description" content="{{ $business->unique_value_proposition ?? $business->description ?? 'Explore ' . $business->name . ' on the UCO Platform.' }}">
        <meta property="og:title" content="{{ $business->name }} - UCO Platform">
        <meta property="og:description" content="{{ $business->unique_value_proposition ?? $business->description ?? 'Explore ' . $business->name . ' on the UCO Platform.' }}">
        <meta property="og:image" content="{{ $business->logo_url ?? asset('images/default-business.png') }}">
        <meta property="og:type" content="business.business">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $business->name }} - UCO Platform">
        <meta name="twitter:description" content="{{ $business->unique_value_proposition ?? $business->description ?? 'Explore ' . $business->name . ' on the UCO Platform.' }}">
        <meta name="twitter:image" content="{{ $business->logo_url ?? asset('images/default-business.png') }}">
    @endpush

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Breadcrumbs & Actions --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <nav class="flex text-sm font-medium" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('businesses.index') }}" class="text-gray-400 hover:text-uco-orange-500 transition">Directory</a></li>
                    <li class="flex items-center space-x-2">
                        <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
                        <span class="text-gray-900">{{ $business->name }}</span>
                    </li>
                </ol>
            </nav>

            @if(Auth::check() && Auth::user()->isAdmin())
                <div class="flex gap-3">
                    <a href="{{ route('businesses.edit', $business) }}" class="px-4 py-2 bg-uco-orange-50 text-uco-orange-600 font-bold rounded-lg border border-uco-orange-200 hover:bg-uco-orange-100 transition">
                        <i class="bi bi-pencil me-2"></i>Edit Business
                    </a>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Business Info --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Hero Section --}}
                <div class="bg-white border border-gray-100 rounded-[2.5rem] p-8 md:p-12 shadow-sm overflow-hidden relative group">
                    <div class="uco-hero-mesh opacity-20 group-hover:opacity-30 transition-opacity duration-700"></div>
                    <div class="relative z-10 flex flex-col md:flex-row gap-10 items-start md:items-center">
                        <div class="w-32 h-32 md:w-48 md:h-48 bg-gray-50 rounded-[2rem] flex items-center justify-center border border-gray-100 shadow-inner flex-shrink-0 overflow-hidden bg-white">
                            @if($business->logo_url)
                                <img src="{{ $business->logo_url }}" class="w-full h-full object-contain p-4 group-hover:scale-105 transition-transform duration-700">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center">
                                    <i class="bi bi-building text-5xl text-gray-200"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 space-y-6">
                            <div class="space-y-4">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-blue-600 ring-1 ring-inset ring-blue-600/10">
                                        {{ $business->category->name ?? 'Business' }}
                                    </span>
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-widest ring-1 ring-inset {{ $business->type === 'entrepreneur' ? 'bg-emerald-50 text-emerald-600 ring-emerald-600/10' : 'bg-purple-50 text-purple-600 ring-purple-600/10' }}">
                                        {{ $business->type }}
                                    </span>
                                </div>
                                <h1 class="text-4xl md:text-5xl font-black text-gray-900 leading-tight tracking-tight">{{ $business->name }}</h1>
                                @if($business->unique_value_proposition)
                                    <p class="text-xl text-uco-orange-500 font-bold leading-relaxed">{{ $business->unique_value_proposition }}</p>
                                @endif
                                @if($business->description)
                                    <p class="text-gray-500 font-medium leading-relaxed max-w-2xl">{{ $business->description }}</p>
                                @endif
                            </div>
                            
                            <div class="flex flex-wrap gap-4 pt-2">
                                @if($business->city || $business->province)
                                    <div class="flex items-center gap-2 text-gray-600 bg-gray-50/50 px-4 py-2 rounded-xl border border-gray-100 font-bold text-xs">
                                        <i class="bi bi-geo-alt-fill text-uco-orange-500"></i>
                                        <span>{{ $business->city }}{{ $business->province ? ', ' . $business->province : '' }}</span>
                                    </div>
                                @endif
                                @if($business->business_scale)
                                    <div class="flex items-center gap-2 text-gray-600 bg-gray-50/50 px-4 py-2 rounded-xl border border-gray-100 font-bold text-xs">
                                        <i class="bi bi-graph-up-arrow text-uco-orange-500"></i>
                                        <span>{{ $business->business_scale }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Products Section (only if products exist) --}}
                @if($business->products->count() > 0)
                    <div class="space-y-6">
                        <h2 class="text-2xl font-bold text-gray-900">Products & Services</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($business->products as $product)
                                @if($product->photo_url)
                                    <div class="bg-white border rounded-2xl overflow-hidden hover:shadow-md transition">
                                        <div class="aspect-video bg-gray-100 flex items-center justify-center overflow-hidden">
                                            <img src="{{ $product->photo_url }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="p-5">
                                            <h3 class="font-bold text-gray-900 text-lg">{{ $product->name }}</h3>
                                            <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ $product->description }}</p>
                                            @if($product->price)
                                                <p class="text-uco-orange-600 font-bold mt-3">{{ $product->price }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-white border rounded-2xl p-5 hover:shadow-md transition">
                                        <h3 class="font-bold text-gray-900 text-lg">{{ $product->name }}</h3>
                                        <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ $product->description }}</p>
                                        @if($product->price)
                                            <p class="text-uco-orange-600 font-bold mt-3">{{ $product->price }}</p>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Legal & Certifications (only if data exists) --}}
                @if($business->legalDocuments->count() > 0 || $business->certifications->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @if($business->legalDocuments->count() > 0)
                            <div class="space-y-4">
                                <h3 class="text-xl font-bold text-gray-900">Legal Documents</h3>
                                <div class="space-y-2">
                                    @foreach($business->legalDocuments as $doc)
                                        <div class="flex items-center gap-3 p-3 bg-gray-50 border rounded-xl">
                                            <i class="bi bi-file-earmark-text text-gray-400"></i>
                                            <span class="text-sm font-medium text-gray-700">{{ $doc->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if($business->certifications->count() > 0)
                            <div class="space-y-4">
                                <h3 class="text-xl font-bold text-gray-900">Certifications</h3>
                                <div class="space-y-2">
                                    @foreach($business->certifications as $cert)
                                        <div class="flex items-center gap-3 p-3 bg-uco-orange-50 border border-uco-orange-100 rounded-xl">
                                            <i class="bi bi-patch-check text-uco-orange-500"></i>
                                            <span class="text-sm font-medium text-gray-700">{{ $cert->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Right Column: Student & Contact --}}
            <div class="space-y-8">
                {{-- UCO Student Card --}}
                @if($business->user)
                    <div class="bg-white border border-gray-100 rounded-[2rem] p-8 shadow-sm group">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">UCO Student Founder</h3>
                        <div class="flex flex-col items-center text-center gap-4 mb-8">
                            <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-gray-50 bg-gray-50 shadow-inner group-hover:scale-105 transition-transform duration-500">
                                @if($business->user->profile_photo_url)
                                    <img src="{{ $business->user->profile_photo_url }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
                                        <i class="bi bi-person text-4xl text-gray-300"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-black text-gray-900 text-2xl leading-tight">{{ $business->user->name }}</h4>
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mt-1">{{ $business->user->major }}</p>
                                <div class="flex items-center justify-center gap-2 mt-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-gray-900 text-white shadow-lg">
                                        {{ $business->user->display_status }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 border-t border-gray-50 pt-8">
                            <div class="flex items-center gap-4 group/item">
                                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover/item:bg-uco-orange-50 group-hover/item:text-uco-orange-500 transition-colors">
                                    <i class="bi bi-envelope-fill text-sm"></i>
                                </div>
                                <span class="text-sm font-bold text-gray-700 truncate">{{ $business->user->email }}</span>
                            </div>
                            @if($business->user->whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business->user->whatsapp) }}" target="_blank" class="flex items-center gap-4 group/item hover:translate-x-1 transition-transform">
                                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover/item:bg-green-50 group-hover/item:text-green-500 transition-colors">
                                        <i class="bi bi-whatsapp text-sm"></i>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700">{{ $business->user->whatsapp }}</span>
                                </a>
                            @endif
                            @if($business->user->linkedin)
                                <a href="{{ $business->user->linkedin }}" target="_blank" class="flex items-center gap-4 group/item hover:translate-x-1 transition-transform">
                                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover/item:bg-blue-50 group-hover/item:text-blue-600 transition-colors">
                                        <i class="bi bi-linkedin text-sm"></i>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700">Connect on LinkedIn</span>
                                </a>
                            @endif
                        </div>

                        {{-- Skills --}}
                        @if($business->user->skills->count() > 0)
                            <div class="mt-8 pt-8 border-t border-gray-50">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Core Competencies</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($business->user->skills as $skill)
                                        <span class="px-3 py-1.5 bg-gray-50 text-gray-600 rounded-xl text-[9px] font-black uppercase tracking-tighter border border-gray-100 hover:bg-white hover:border-uco-orange-200 transition-all cursor-default">{{ $skill->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Related Students (from pivot) --}}
                @if($business->members->count() > 1)
                    <div class="bg-white border rounded-3xl p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Team Members</h3>
                        <div class="space-y-4">
                            @foreach($business->members as $member)
                                @if($member->id !== $business->user_id)
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-100 border flex items-center justify-center">
                                            @if($member->profile_photo_url)
                                                <img src="{{ $member->profile_photo_url }}" class="w-full h-full object-cover">
                                            @else
                                                <i class="bi bi-person text-gray-300"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $member->name }}</p>
                                            <p class="text-[10px] text-gray-500">{{ $member->pivot->position ?? $member->major }}</p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Business Contact --}}
                <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-2xl overflow-hidden relative group">
                    <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-48 h-48 bg-uco-orange-500/20 rounded-full blur-[80px] group-hover:bg-uco-orange-500/30 transition-colors duration-700"></div>
                    <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-8 relative z-10">Business Concierge</h3>
                    <div class="space-y-6 relative z-10">
                        @if($business->phone_number)
                            <a href="tel:{{ $business->phone_number }}" class="flex flex-col group/link">
                                <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Phone Line</span>
                                <span class="font-bold text-lg group-hover/link:text-uco-orange-400 transition-colors">{{ $business->phone_number }}</span>
                            </a>
                        @endif
                        @if($business->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}" target="_blank" class="flex flex-col group/link">
                                <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Direct WhatsApp</span>
                                <span class="font-bold text-lg group-hover/link:text-green-400 transition-colors">{{ $business->whatsapp }}</span>
                            </a>
                        @endif
                        @if($business->email)
                            <a href="mailto:{{ $business->email }}" class="flex flex-col group/link">
                                <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Corporate Email</span>
                                <span class="font-bold group-hover/link:text-uco-orange-400 transition-colors truncate">{{ $business->email }}</span>
                            </a>
                        @endif
                        @if($business->website)
                            <a href="{{ $business->website }}" target="_blank" class="flex flex-col group/link">
                                <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Official Website</span>
                                <span class="font-black text-xl text-uco-orange-500 group-hover/link:text-uco-orange-400 transition-all group-hover/link:translate-x-1 break-all">{{ parse_url($business->website, PHP_URL_HOST) ?? $business->website }}</span>
                            </a>
                        @endif
                        @if($business->instagram)
                            <a href="https://instagram.com/{{ ltrim($business->instagram, '@') }}" target="_blank" class="flex flex-col group/link">
                                <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Social Feed</span>
                                <span class="font-bold group-hover/link:text-pink-400 transition-colors italic">@ {{ ltrim($business->instagram, '@') }}</span>
                            </a>
                        @endif
                    </div>
                    @if($business->address)
                        <div class="mt-10 pt-8 border-t border-white/10 relative z-10">
                            <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-3">Headquarters</p>
                            <p class="text-sm text-gray-400 font-medium leading-relaxed">{{ $business->address }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
