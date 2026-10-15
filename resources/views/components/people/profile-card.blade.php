{{-- --- Person card on a business or work-profile page: photo, academics and contacts (the slot adds extra rows, e.g. skills) --- --}}
@props(['person', 'titleStart', 'titleEnd', 'status', 'career', 'withLinkedin' => false])

<div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden transition hover:shadow-md duration-300 group/card hover:border-orange-200 cursor-pointer">
    {{-- --- Invisible link that stretches over the whole card --- --}}
    <a href="{{ route('users.show', $person) }}" class="absolute inset-0 z-10" aria-label="View {{ $person->name }} Profile"></a>

    {{-- --- Arrow Icon --- --}}
    <div class="absolute top-4 right-4 text-gray-300 group-hover/card:text-orange-500 transition-colors z-10">
        <i class="bi bi-box-arrow-up-right text-xs"></i>
    </div>

    {{-- --- Section Title --- --}}
    <div class="flex items-center gap-2 mb-6 relative z-10 pointer-events-none">
        <span class="w-1.5 h-6 bg-gradient-to-b from-[#f7931e] to-[#fdb913] rounded-full flex-shrink-0"></span>
        <h4 class="text-base font-black uppercase tracking-[0.15em] text-gray-700">{{ $titleStart }} <span class="uco-text-gradient-orange">{{ $titleEnd }}</span></h4>
    </div>

    {{-- --- Header: Avatar & Name --- --}}
    <div class="flex items-center gap-5 relative z-10 pointer-events-none">
        <div class="w-16 h-16 md:w-20 md:h-20 rounded-sm overflow-hidden flex-shrink-0 flex items-center justify-center">
            @if ($person->profile_photo_thumb_url)
            <x-premium-image :src="$person->profile_photo_thumb_url" :alt="$person->name" class="w-full h-full" />
            @else
                <span class="text-3xl font-black opacity-20 select-none">{{ strtoupper(substr($person->name, 0, 1)) }}</span>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <h2 class="text-lg md:text-xl font-extrabold text-gray-900 leading-tight tracking-tight truncate">{{ $person->name }}</h2>
            <p class="text-gray-400 font-bold text-[11px] mt-1 tracking-[0.1em] uppercase">{{ $status ?: 'Active' }}</p>
        </div>
    </div>

    <div class="w-full h-px bg-gray-100 my-5"></div>

    {{-- --- Academic Details --- --}}
    <div class="space-y-3">
        <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] mb-3">Academic Details</h3>
        <div class="flex items-center justify-between">
            <span class="text-[13px] font-medium text-gray-500">Major</span>
            <span class="text-[13px] font-bold text-gray-900">{{ $person->major ?: '-' }}</span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-[13px] font-medium text-gray-500">Join UC Online</span>
            <span class="text-[13px] font-bold text-gray-900">{{ $person->year_of_enrollment ?: '-' }}</span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-[13px] font-medium text-gray-500">Focus</span>
            <span class="text-[13px] font-bold text-gray-900">{{ $career }}</span>
        </div>
    </div>

    {{-- --- Contacts --- --}}
    @if(($person->whatsapp || $person->email || ($withLinkedin && $person->linkedin)) && ($person->show_contact_details || (Auth::check() && Auth::user()->isAdmin())))
    <div class="w-full h-px bg-gray-100 my-5 relative z-10 pointer-events-none"></div>
    <div class="space-y-4 relative z-20">
        @if($person->whatsapp)
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $person->whatsapp) }}" target="_blank" class="flex items-center justify-between group cursor-pointer">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-green-50 text-green-500 flex items-center justify-center group-hover:bg-green-100 transition-colors">
                    <i class="bi bi-whatsapp text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-tight">WhatsApp</p>
                    <p class="text-sm font-bold text-gray-800 group-hover:text-green-600 transition-colors leading-tight mt-0.5">{{ $person->whatsapp }}</p>
                </div>
            </div>
            <i class="bi bi-arrow-up-right text-gray-300 group-hover:text-green-500 transition-colors text-sm"></i>
        </a>
        @endif

        @if($person->email)
        <a href="mailto:{{ $person->email }}" class="flex items-center justify-between group cursor-pointer">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                    <i class="bi bi-envelope text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-tight">Email</p>
                    <p class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition-colors leading-tight mt-0.5">{{ $person->email }}</p>
                </div>
            </div>
            <i class="bi bi-arrow-up-right text-gray-300 group-hover:text-blue-500 transition-colors text-sm"></i>
        </a>
        @endif
        @if($withLinkedin && $person->linkedin)
        <a href="{{ str_starts_with($person->linkedin, 'http') ? $person->linkedin : 'https://linkedin.com/in/' . $person->linkedin }}" target="_blank" class="flex items-center justify-between group cursor-pointer">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                    <i class="bi bi-linkedin text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-tight">LinkedIn</p>
                    <p class="text-sm font-bold text-gray-800 group-hover:text-indigo-600 transition-colors leading-tight mt-0.5">{{ $person->linkedin }}</p>
                </div>
            </div>
            <i class="bi bi-arrow-up-right text-gray-300 group-hover:text-indigo-500 transition-colors text-sm"></i>
        </a>
        @endif
    </div>
    @elseif($person->whatsapp || $person->email || ($withLinkedin && $person->linkedin))
    <div class="w-full h-px bg-gray-100 my-5 relative z-10 pointer-events-none"></div>
    <div class="px-5 py-2 relative z-20 text-center">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed">
            <i class="bi bi-lock-fill mr-1"></i> Contact details hidden by user
        </p>
    </div>
    @endif

    {{ $slot }}
</div>
