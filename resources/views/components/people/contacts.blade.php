{{-- --- Contact links of a person, or a note when they keep them private; nothing when they have none --- --}}
@props(['person', 'withLinkedin' => true])

@if ($person->hasContactDetails($withLinkedin) && $person->contactsVisibleTo(auth()->user()))
    <div class="space-y-2 border-t border-line pt-4">
        @if ($person->whatsapp)
            <x-people.contact-link :href="$person->whatsapp_link" icon="bi-whatsapp" label="WhatsApp" new-tab>{{ $person->whatsapp }}</x-people.contact-link>
        @endif
        @if ($person->email)
            <x-people.contact-link :href="'mailto:' . $person->email" icon="bi-envelope" label="Email">{{ $person->email }}</x-people.contact-link>
        @endif
        @if ($withLinkedin && $person->linkedin)
            <x-people.contact-link :href="$person->linkedin_url" icon="bi-linkedin" label="LinkedIn" new-tab>{{ $person->linkedin }}</x-people.contact-link>
        @endif
    </div>
@elseif ($person->hasContactDetails($withLinkedin))
    <p class="border-t border-line pt-4 text-sm text-gray-600"><i class="bi bi-lock" aria-hidden="true"></i> This person keeps their contact details private.</p>
@endif
