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
                    <li><a href="{{ route('businesses.index') }}" class="text-gray-400 hover:text-gray-700 transition-colors">Directory</a></li>
                    <li class="flex items-center space-x-2">
                        <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
                        <span class="text-gray-800 font-medium">{{ $business->name }}</span>
                    </li>
                </ol>
            </nav>

            @if(Auth::check() && Auth::user()->isAdmin())
                <div class="flex gap-3">
                    <a href="{{ route('businesses.edit', $business) }}" class="px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-800 font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                        <i class="bi bi-pencil text-sm"></i>
                        <span>Edit Business</span>
                    </a>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: All Business Info --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Hero Section --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-10 shadow-sm overflow-hidden relative group transition duration-300 hover:shadow-md">
                    <div class="relative z-10 flex flex-col md:flex-row gap-8 items-start md:items-center">
                        <div class="w-24 h-24 md:w-36 md:h-36 bg-white rounded-2xl flex items-center justify-center border border-gray-100 flex-shrink-0 overflow-hidden hover:border-gray-200 transition-all duration-500 shadow-sm">
                            @if($business->logo_url)
                                <img src="{{ $business->logo_url }}" class="w-full h-full object-contain p-2 group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-300 select-none">
                                    <span class="text-4xl font-black opacity-30 tracking-tighter">{{ substr($business->name, 0, 2) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 space-y-4">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($business->category)
                                        <span class="inline-flex items-center rounded-lg bg-gray-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-600 border border-gray-100">
                                            {{ $business->category->name }}
                                        </span>
                                    @endif
                                    @if($business->type)
                                        <span class="inline-flex items-center rounded-lg bg-gray-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-600 border border-gray-100">
                                            {{ $business->type }}
                                        </span>
                                    @endif
                                    @if($business->operational_status)
                                        <span class="inline-flex items-center rounded-lg bg-gray-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-600 border border-gray-100">
                                            {{ $business->operational_status }}
                                        </span>
                                    @endif
                                </div>
                                <h1 class="text-2xl md:text-4xl font-extrabold text-gray-900 leading-tight tracking-tight">{{ $business->name }}</h1>
                                @if($business->unique_value_proposition)
                                    <p class="text-base md:text-lg text-gray-700 font-semibold leading-relaxed">{{ $business->unique_value_proposition }}</p>
                                @endif
                                @if($business->description)
                                    <p class="text-gray-500 font-normal leading-relaxed max-w-2xl text-sm">{{ $business->description }}</p>
                                @endif
                            </div>
                            
                            <div class="flex flex-wrap gap-3 pt-1">
                                @if($business->city || $business->province)
                                    <div class="flex items-center gap-2 text-gray-600 bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-100 font-semibold text-xs">
                                        <i class="bi bi-geo-alt text-gray-400"></i>
                                        <span>{{ $business->city }}{{ $business->province ? ', ' . $business->province : '' }}</span>
                                    </div>
                                @endif
                                @if($business->business_scale)
                                    <div class="flex items-center gap-2 text-gray-600 bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-100 font-semibold text-xs">
                                        <i class="bi bi-graph-up text-gray-400"></i>
                                        <span>{{ $business->business_scale }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Products Section --}}
                @if($business->products->count() > 0)
                    <div class="space-y-6">
                        <h2 class="text-lg font-bold text-gray-900 tracking-tight flex items-center gap-3">
                            <span class="w-1 h-5 bg-gray-800 rounded-full"></span>
                            Products & Services
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($business->products as $product)
                                <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-lg hover:border-gray-200 transition-all duration-300 flex flex-col h-full group">
                                    <div class="aspect-video bg-gray-50 flex items-center justify-center overflow-hidden relative border-b border-gray-100">
                                        @if($product->photo_url)
                                            <img src="{{ $product->photo_url }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-200 select-none">
                                                <span class="text-4xl font-black opacity-20 tracking-tighter">{{ substr($product->name, 0, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-5 flex flex-col justify-between flex-1 space-y-4">
                                        <div class="space-y-2">
                                            <h3 class="font-bold text-gray-900 text-base leading-snug group-hover:text-gray-600 transition-colors">{{ $product->name }}</h3>
                                            @if($product->description)
                                                <p class="text-xs text-gray-500 font-normal leading-relaxed line-clamp-3">{{ $product->description }}</p>
                                            @endif
                                        </div>
                                        @if($product->price)
                                            <div class="pt-2 border-t border-gray-50">
                                                <p class="text-gray-900 font-extrabold text-base tracking-tight">
                                                    {{ is_numeric($product->price) ? 'Rp ' . number_format($product->price, 0, ',', '.') : $product->price }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Business Contact --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-sm transition hover:shadow-md duration-300">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Business Contacts</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($business->phone_number)
                            <a href="tel:{{ $business->phone_number }}" class="flex flex-col p-4 bg-gray-50/50 border border-gray-100 rounded-xl hover:border-gray-200 transition duration-300 group">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Phone Line</span>
                                <span class="font-bold text-gray-800 text-sm group-hover:text-gray-900 transition-colors">{{ $business->phone_number }}</span>
                            </a>
                        @endif
                        @if($business->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}" target="_blank" class="flex flex-col p-4 bg-gray-50/50 border border-gray-100 rounded-xl hover:border-gray-200 transition duration-300 group">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Direct WhatsApp</span>
                                <span class="font-bold text-gray-800 text-sm group-hover:text-gray-900 transition-colors">{{ $business->whatsapp }}</span>
                            </a>
                        @endif
                        @if($business->email)
                            <a href="mailto:{{ $business->email }}" class="flex flex-col p-4 bg-gray-50/50 border border-gray-100 rounded-xl hover:border-gray-200 transition duration-300 group">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Corporate Email</span>
                                <span class="font-bold text-gray-800 text-sm group-hover:text-gray-900 transition-colors truncate">{{ $business->email }}</span>
                            </a>
                        @endif
                        @if($business->website)
                            <a href="{{ $business->website }}" target="_blank" class="flex flex-col p-4 bg-gray-50/50 border border-gray-100 rounded-xl hover:border-gray-200 transition duration-300 group">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Official Website</span>
                                <span class="font-bold text-gray-800 text-sm group-hover:text-gray-900 transition-all truncate">{{ parse_url($business->website, PHP_URL_HOST) ?? $business->website }}</span>
                            </a>
                        @endif
                        @if($business->instagram)
                            <a href="https://instagram.com/{{ ltrim($business->instagram, '@') }}" target="_blank" class="flex flex-col p-4 bg-gray-50/50 border border-gray-100 rounded-xl hover:border-gray-200 transition duration-300 group">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Social Feed</span>
                                <span class="font-bold text-gray-800 text-sm group-hover:text-gray-900 transition-colors">@ {{ ltrim($business->instagram, '@') }}</span>
                            </a>
                        @endif
                    </div>
                    @if($business->address)
                        <div class="mt-6 pt-5 border-t border-gray-100">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Headquarters</p>
                            <p class="text-sm text-gray-600 font-medium leading-relaxed">{{ $business->address }}</p>
                        </div>
                    @endif
                </div>

                {{-- Legal & Certifications --}}
                @if($business->legalDocuments->count() > 0 || $business->certifications->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($business->legalDocuments->count() > 0)
                            <div class="space-y-3">
                                <h3 class="text-base font-bold text-gray-900">Legal Documents</h3>
                                <div class="space-y-2">
                                    @foreach($business->legalDocuments as $doc)
                                        <div class="flex items-center gap-3 p-3 bg-white border border-gray-100 rounded-xl hover:border-gray-200 transition-colors duration-300">
                                            <i class="bi bi-file-earmark-text text-gray-400"></i>
                                            <span class="text-sm font-semibold text-gray-700">{{ $doc->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if($business->certifications->count() > 0)
                            <div class="space-y-3">
                                <h3 class="text-base font-bold text-gray-900">Certifications</h3>
                                <div class="space-y-2">
                                    @foreach($business->certifications as $cert)
                                        <div class="flex items-center gap-3 p-3 bg-white border border-gray-100 rounded-xl hover:border-gray-200 transition-colors duration-300">
                                            <i class="bi bi-patch-check text-gray-500"></i>
                                            <span class="text-sm font-semibold text-gray-700">{{ $cert->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Right Column: ONLY Users/Owners (Founder + Team Members) --}}
            <div class="space-y-8">
                @if($business->user)
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm transition hover:shadow-md duration-300 group">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-5">UCO Student Founder</h3>
                        <div class="flex flex-col items-center text-center gap-3 mb-6">
                            <a href="{{ route('users.show', $business->user) }}" class="w-20 h-20 rounded-xl overflow-hidden border border-gray-100 bg-gray-50 flex items-center justify-center hover:scale-105 transition-transform duration-500 shadow-sm">
                                @if($business->user->profile_photo_url)
                                    <img src="{{ $business->user->profile_photo_url }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 select-none">
                                        <span class="text-3xl font-black opacity-20 select-none">{{ substr($business->user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </a>
                            <div>
                                <a href="{{ route('users.show', $business->user) }}" class="font-bold text-gray-900 text-lg leading-tight hover:text-gray-700 transition">
                                    {{ $business->user->name }}
                                </a>
                                @if($business->user->major)
                                    <p class="text-xs text-gray-400 font-medium tracking-wider mt-1">{{ $business->user->major }}</p>
                                @endif
                                <div class="flex items-center justify-center gap-2 mt-2">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-bold tracking-widest bg-gray-100 text-gray-600 border border-gray-200 uppercase">
                                        {{ $business->user->display_status }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 border-t border-gray-100 pt-5">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                                    <i class="bi bi-envelope text-xs"></i>
                                </div>
                                <span class="text-xs font-medium text-gray-600 truncate">{{ $business->user->email }}</span>
                            </div>
                            @if($business->user->whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business->user->whatsapp) }}" target="_blank" class="flex items-center gap-3 group/item hover:translate-x-0.5 transition-transform">
                                    <div class="w-6 h-6 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover/item:text-emerald-600 transition-colors">
                                        <i class="bi bi-whatsapp text-xs"></i>
                                    </div>
                                    <span class="text-xs font-medium text-gray-600">{{ $business->user->whatsapp }}</span>
                                </a>
                            @endif
                            @if($business->user->linkedin)
                                <a href="{{ $business->user->linkedin }}" target="_blank" class="flex items-center gap-3 group/item hover:translate-x-0.5 transition-transform">
                                    <div class="w-6 h-6 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover/item:text-blue-600 transition-colors">
                                        <i class="bi bi-linkedin text-xs"></i>
                                    </div>
                                    <span class="text-xs font-medium text-gray-600">LinkedIn</span>
                                </a>
                            @endif
                        </div>

                        {{-- Additional Owners / Team --}}
                        @if($business->members->where('id', '!=', $business->user_id)->count() > 0)
                            <div class="mt-8 pt-6 border-t border-gray-100">
                                <p class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Additional Owners / Team</p>
                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($business->members as $member)
                                        @if($member->id !== $business->user_id)
                                            <a href="{{ route('users.show', $member) }}" class="p-4 bg-white border border-gray-100 rounded-xl flex items-center gap-4 hover:border-gray-200 hover:shadow-md transition-all duration-300 group">
                                                <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                                    @if($member->profile_photo_url)
                                                        <img src="{{ $member->profile_photo_url }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 text-gray-400 text-sm font-black select-none">
                                                            {{ substr($member->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="overflow-hidden flex-1">
                                                    <p class="text-sm font-bold text-gray-900 truncate group-hover:text-gray-700 transition">{{ $member->name }}</p>
                                                    @if($member->pivot && $member->pivot->position)
                                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider truncate mt-0.5">{{ $member->pivot->position }}</p>
                                                    @elseif($member->major)
                                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider truncate mt-0.5">{{ $member->major }}</p>
                                                    @endif
                                                </div>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Relevant / Recommended Students section --}}
                        @php
                            $recommendedUsers = \App\Models\User::where('is_visible', true)
                                ->where('id', '!=', $business->user_id)
                                ->whereHas('businesses', fn($q) => $q->where('is_visible', true))
                                ->inRandomOrder()
                                ->take(3)
                                ->get();
                        @endphp
                        @if($recommendedUsers->count() > 0)
                            <div class="mt-8 pt-6 border-t border-gray-100">
                                <p class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Recommended Students</p>
                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($recommendedUsers as $recUser)
                                        <a href="{{ route('users.show', $recUser) }}" class="p-4 bg-white border border-gray-100 rounded-xl flex items-center gap-4 hover:border-gray-200 hover:shadow-md transition-all duration-300 group">
                                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                                @if($recUser->profile_photo_url)
                                                    <img src="{{ $recUser->profile_photo_url }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 text-gray-400 text-sm font-black select-none">
                                                        {{ substr($recUser->name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="overflow-hidden flex-1">
                                                <p class="text-sm font-bold text-gray-900 truncate group-hover:text-gray-700 transition">{{ $recUser->name }}</p>
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider truncate mt-0.5">{{ $recUser->major ?: 'UCO Student' }}</p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
