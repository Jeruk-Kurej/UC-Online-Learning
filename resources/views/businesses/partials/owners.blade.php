{{-- --- Business page side column: the owner and the other people who run it --- --}}
<x-people.profile-card :person="$owner" title="Business owner" :status="$owner->display_status" :career="$owner->current_status" />

@if ($coOwners->isNotEmpty())
    <section class="card">
        <h2 class="text-sm font-semibold text-gray-600">Co-owners</h2>
        <ul class="mt-3 divide-y divide-line">
            @foreach ($coOwners as $addOwner)
                <li>
                    <a href="{{ route('users.show', $addOwner) }}" class="flex items-center gap-3 py-3 hover:underline">
                        <x-ui.thumb :src="$addOwner->profile_photo_mini_url" :name="$addOwner->name" size="h-10 w-10" round class="text-base" />
                        <span class="min-w-0">
                            <span class="block truncate text-base font-medium text-brand-900">{{ $addOwner->name }}</span>
                            <span class="block truncate text-sm text-gray-600">{{ $addOwner->pivot->position ?? 'Member' }}</span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>
@endif
