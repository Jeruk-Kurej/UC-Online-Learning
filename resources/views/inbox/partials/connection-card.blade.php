{{-- --- One accepted connection, shown from the signed-in user's side ($partner is the other person) --- --}}
<div class="flex flex-col justify-between rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-md">
    <div>
        <div class="mb-4 flex items-start gap-4">
            <a href="{{ route('users.show', $partner) }}" class="flex-shrink-0">
                @if ($partner->profile_photo_url)
                    <img src="{{ $partner->profile_photo_url }}" class="h-14 w-14 rounded-lg border border-gray-200 object-cover">
                @else
                    <div class="flex h-14 w-14 items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-xl font-extrabold text-gray-500">
                        {{ substr($partner->name, 0, 1) }}
                    </div>
                @endif
            </a>
            <div>
                <h3 class="text-base font-bold text-gray-900 transition-colors hover:text-uco-orange-600">
                    <a href="{{ route('users.show', $partner) }}">
                        {{ $partner->name }}
                    </a>
                </h3>
                <p class="mt-0.5 text-xs font-medium text-gray-500">{{ $partner->major }}</p>
                <span class="mt-1.5 inline-block rounded bg-orange-50 px-2 py-0.5 text-[10px] font-bold uppercase text-orange-700">
                    {{ $partner->current_status }}
                </span>
            </div>
        </div>

        @if ($partner->businesses->first())
            <div class="mb-4 rounded-lg border border-gray-100 bg-gray-50 p-3">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Venture / Business</span>
                <span class="mt-0.5 block text-sm font-bold text-gray-800">{{ $partner->businesses->first()->name }}</span>
                <span class="mt-0.5 block text-xs text-gray-500">{{ $partner->businesses->first()->city }}, {{ $partner->businesses->first()->province }}</span>
            </div>
        @endif
    </div>

    <div class="mt-2 flex items-center gap-2 border-t border-gray-100 pt-4">
        @if ($partner->show_contact_details)
            @if ($partner->whatsapp)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $partner->whatsapp) }}" target="_blank"
                   class="inline-flex h-9 flex-1 items-center justify-center gap-1.5 rounded-lg border border-green-300 bg-green-50 text-xs font-bold text-green-700 transition-all hover:bg-green-100">
                    <i class="bi bi-whatsapp"></i> Chat WA
                </a>
            @endif
            @if ($partner->personal_email)
                <a href="mailto:{{ $partner->personal_email }}"
                   class="inline-flex h-9 flex-1 items-center justify-center gap-1.5 rounded-lg border border-blue-300 bg-blue-50 text-xs font-bold text-blue-700 transition-all hover:bg-blue-100">
                    <i class="bi bi-envelope"></i> Email
                </a>
            @endif
        @else
            <span class="inline-flex h-9 flex-1 items-center justify-center gap-1.5 rounded-lg border border-dashed border-gray-300 bg-gray-100 text-xs font-semibold text-gray-500">
                <i class="bi bi-shield-lock-fill"></i> Details Hidden
            </span>
        @endif

        @if ($partner->businesses->first())
            <a href="{{ route('businesses.show', $partner->businesses->first()->slug) }}"
               class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 transition-all hover:border-gray-900 hover:bg-gray-900 hover:text-white"
               title="View Business Profile">
                <i class="bi bi-arrow-right"></i>
            </a>
        @endif
    </div>
</div>
