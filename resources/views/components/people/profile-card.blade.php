{{-- --- Person card on a business or work-profile page: photo, academics and contacts (the slot adds extra rows, e.g. skills) --- --}}
@props(['person', 'titleStart', 'titleEnd', 'status', 'career', 'withLinkedin' => false])

<div class="group/card relative cursor-pointer overflow-hidden rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition duration-300 hover:border-orange-200 hover:shadow-md md:p-8">
    {{-- --- Invisible link that stretches over the whole card --- --}}
    <a href="{{ route('users.show', $person) }}" class="absolute inset-0 z-10" aria-label="View {{ $person->name }} Profile"></a>

    {{-- --- Arrow Icon --- --}}
    <div class="absolute right-4 top-4 z-10 text-gray-300 transition-colors group-hover/card:text-orange-500">
        <i class="bi bi-box-arrow-up-right text-xs"></i>
    </div>

    {{-- --- Section Title --- --}}
    <div class="pointer-events-none relative z-10 mb-6 flex items-center gap-2">
        <span class="h-6 w-1.5 flex-shrink-0 rounded-full bg-gradient-to-b from-[#f7931e] to-[#fdb913]"></span>
        <h4 class="text-base font-black uppercase tracking-[0.15em] text-gray-700">{{ $titleStart }} <span class="uco-text-gradient-orange">{{ $titleEnd }}</span></h4>
    </div>

    {{-- --- Header: Avatar & Name --- --}}
    <div class="pointer-events-none relative z-10 flex items-center gap-5">
        <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center overflow-hidden rounded-sm md:h-20 md:w-20">
            @if ($person->profile_photo_thumb_url)
                <x-premium-image :src="$person->profile_photo_thumb_url" :alt="$person->name" class="h-full w-full" />
            @else
                <span class="select-none text-3xl font-black opacity-20">{{ $person->initial }}</span>
            @endif
        </div>
        <div class="min-w-0 flex-1">
            <h2 class="truncate text-lg font-extrabold leading-tight tracking-tight text-gray-900 md:text-xl">{{ $person->name }}</h2>
            <p class="mt-1 text-[11px] font-bold uppercase tracking-[0.1em] text-gray-400">{{ $status ?: 'Active' }}</p>
        </div>
    </div>

    <div class="my-5 h-px w-full bg-gray-100"></div>

    {{-- --- Academic Details --- --}}
    <div class="space-y-3">
        <h3 class="mb-3 text-[11px] font-black uppercase tracking-[0.15em] text-gray-400">Academic Details</h3>
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
    @if ($person->hasContactDetails($withLinkedin) && $person->contactsVisibleTo(auth()->user()))
        <div class="pointer-events-none relative z-10 my-5 h-px w-full bg-gray-100"></div>
        <div class="relative z-20 space-y-4">
            @if ($person->whatsapp)
                <x-people.contact-tile :href="$person->whatsapp_link" icon="bi-whatsapp" label="WhatsApp" tone="green" new-tab>{{ $person->whatsapp }}</x-people.contact-tile>
            @endif
            @if ($person->email)
                <x-people.contact-tile :href="'mailto:' . $person->email" icon="bi-envelope" label="Email" tone="blue">{{ $person->email }}</x-people.contact-tile>
            @endif
            @if ($withLinkedin && $person->linkedin)
                <x-people.contact-tile :href="$person->linkedin_url" icon="bi-linkedin" label="LinkedIn" tone="indigo" new-tab>{{ $person->linkedin }}</x-people.contact-tile>
            @endif
        </div>
    @elseif ($person->hasContactDetails($withLinkedin))
        <div class="pointer-events-none relative z-10 my-5 h-px w-full bg-gray-100"></div>
        <div class="relative z-20 px-5 py-2 text-center">
            <p class="text-[10px] font-bold uppercase leading-relaxed tracking-widest text-gray-400">
                <i class="bi bi-lock-fill mr-1"></i> Contact details hidden by user
            </p>
        </div>
    @endif

    {{ $slot }}
</div>
