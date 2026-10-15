{{-- --- One accepted connection, shown from the signed-in user's side ($partner is the other person) --- --}}
<div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
    <div>
        <div class="flex items-start gap-4 mb-4">
            <a href="{{ route('users.show', $partner) }}" class="flex-shrink-0">
                @if($partner->profile_photo_url)
                    <img src="{{ $partner->profile_photo_url }}" class="w-14 h-14 rounded-lg object-cover border border-gray-200">
                @else
                    <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center text-xl font-extrabold text-gray-500 border border-gray-200">
                        {{ substr($partner->name, 0, 1) }}
                    </div>
                @endif
            </a>
            <div>
                <h3 class="text-base font-bold text-gray-900 hover:text-uco-orange-600 transition-colors">
                    <a href="{{ route('users.show', $partner) }}">
                        {{ $partner->name }}
                    </a>
                </h3>
                <p class="text-xs text-gray-500 font-medium mt-0.5">{{ $partner->major }}</p>
                <span class="inline-block mt-1.5 px-2 py-0.5 bg-orange-50 text-orange-700 rounded text-[10px] font-bold uppercase">
                    {{ $partner->current_status }}
                </span>
            </div>
        </div>

        @if($partner->businesses->first())
            <div class="bg-gray-50 rounded-lg p-3 mb-4 border border-gray-100">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Venture / Business</span>
                <span class="text-sm font-bold text-gray-800 block mt-0.5">{{ $partner->businesses->first()->name }}</span>
                <span class="text-xs text-gray-500 block mt-0.5">{{ $partner->businesses->first()->city }}, {{ $partner->businesses->first()->province }}</span>
            </div>
        @endif
    </div>

    <div class="flex items-center gap-2 pt-4 border-t border-gray-100 mt-2">
        @if($partner->show_contact_details)
            @if($partner->whatsapp)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $partner->whatsapp) }}" target="_blank" 
                   class="flex-1 inline-flex items-center justify-center gap-1.5 h-9 rounded-lg border border-green-300 bg-green-50 hover:bg-green-100 text-green-700 font-bold text-xs transition-all">
                    <i class="bi bi-whatsapp"></i> Chat WA
                </a>
            @endif
            @if($partner->personal_email)
                <a href="mailto:{{ $partner->personal_email }}" 
                   class="flex-1 inline-flex items-center justify-center gap-1.5 h-9 rounded-lg border border-blue-300 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs transition-all">
                    <i class="bi bi-envelope"></i> Email
                </a>
            @endif
        @else
            <span class="flex-1 inline-flex items-center justify-center gap-1.5 h-9 rounded-lg bg-gray-100 text-gray-500 text-xs font-semibold border border-dashed border-gray-300">
                <i class="bi bi-shield-lock-fill"></i> Details Hidden
            </span>
        @endif

        @if($partner->businesses->first())
            <a href="{{ route('businesses.show', $partner->businesses->first()->slug) }}" 
               class="w-9 h-9 rounded-lg border border-gray-200 hover:border-gray-900 bg-white hover:bg-gray-900 text-gray-700 hover:text-white flex items-center justify-center transition-all"
               title="View Business Profile">
                <i class="bi bi-arrow-right"></i>
            </a>
        @endif
    </div>
</div>
