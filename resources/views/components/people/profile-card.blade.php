{{-- --- Person card on a business or work-profile page: photo, name, academics and contacts --- --}}
@props(['person', 'title', 'status', 'career', 'withLinkedin' => false])

<section class="card space-y-4" aria-labelledby="person-{{ $person->id }}">
    <h2 class="text-sm font-semibold text-gray-600">{{ $title }}</h2>

    <div class="flex items-center gap-4">
        <x-ui.thumb :src="$person->profile_photo_thumb_url" :name="$person->name" size="h-16 w-16" round class="text-2xl" />
        <div class="min-w-0 flex-1">
            <a id="person-{{ $person->id }}" href="{{ route('users.show', $person) }}" class="block truncate text-lg font-semibold text-brand-900 hover:underline">{{ $person->name }}</a>
            <p class="text-sm text-gray-600">{{ ucfirst($status ?: 'active') }}</p>
        </div>
    </div>

    @if ($person->major || $person->year_of_enrollment || $career)
        <dl class="divide-y divide-line border-t border-line">
            @if ($person->major)
                <x-ui.fact label="Major">{{ $person->major }}</x-ui.fact>
            @endif
            @if ($person->year_of_enrollment)
                <x-ui.fact label="Joined UC Online">{{ $person->year_of_enrollment }}</x-ui.fact>
            @endif
            @if ($career)
                <x-ui.fact label="Role">{{ $career }}</x-ui.fact>
            @endif
        </dl>
    @endif

    <x-people.contacts :person="$person" :with-linkedin="$withLinkedin" />

    {{ $slot }}
</section>
