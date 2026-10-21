{{-- --- One accepted connection, shown from the signed-in user's side ($partner is the other person) --- --}}
<article class="card flex h-full flex-col gap-4">
    <div class="flex items-center gap-4">
        <x-ui.thumb :src="$partner->profile_photo_url" :name="$partner->name" size="h-14 w-14" round class="text-xl" />
        <div class="min-w-0">
            <a href="{{ route('users.show', $partner) }}" class="block truncate text-lg font-semibold text-brand-900 hover:underline">{{ $partner->name }}</a>
            <p class="truncate text-sm text-gray-600">{{ $partner->major }}</p>
        </div>
    </div>

    @if ($partner->businesses->first())
        <p class="text-base text-gray-800"><a href="{{ route('businesses.show', $partner->businesses->first()->slug) }}" class="text-link">{{ $partner->businesses->first()->name }}</a></p>
    @endif

    {{-- --- Connected people see each other's contact details (the accept screen says so) --- --}}
    <div class="mt-auto flex flex-wrap gap-2 border-t border-line pt-4">
        @if ($partner->whatsapp)
            <a href="{{ $partner->whatsapp_link }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm flex-1"><i class="bi bi-whatsapp" aria-hidden="true"></i> WhatsApp</a>
        @endif
        <a href="mailto:{{ $partner->contact_email }}" class="btn btn-secondary btn-sm flex-1"><i class="bi bi-envelope" aria-hidden="true"></i> Email</a>
        @if ($partner->linkedin)
            <a href="{{ $partner->linkedin_url }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm flex-1"><i class="bi bi-linkedin" aria-hidden="true"></i> LinkedIn</a>
        @endif
    </div>
</article>
