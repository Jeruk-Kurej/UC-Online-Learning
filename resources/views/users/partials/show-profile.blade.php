{{-- --- Profile page left column: photo, name, buttons, academics, contacts, skills, certificates and activities --- --}}
<div class="space-y-6">
    <section class="card space-y-4">
        <div class="flex items-center gap-4">
            <x-ui.thumb :src="$user->profile_photo_url" :name="$user->name" size="h-24 w-24" round class="text-4xl" />
            <div class="min-w-0">
                <h1 class="break-words font-serif text-2xl font-semibold leading-tight text-brand-900">{{ $user->name }}</h1>
                <p class="mt-1 text-base text-gray-600">{{ $user->isIntrapreneur() ? 'Intrapreneur' : 'Entrepreneur' }}</p>
            </div>
        </div>

        @if (Auth::check() && (Auth::user()->isAdmin() || Auth::id() !== $user->id))
            <div class="flex flex-wrap gap-2">
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-pencil" aria-hidden="true"></i>
                        Edit user
                    </a>
                @endif

                @if (Auth::id() !== $user->id)
                    @if ($collabStatus === 'accepted')
                        <span class="badge badge-success text-sm"><i class="bi bi-person-check" aria-hidden="true"></i> Connected</span>
                    @elseif ($collabStatus === 'pending')
                        <span class="badge badge-warning text-sm"><i class="bi bi-clock-history" aria-hidden="true"></i> Collab request sent</span>
                    @else
                        <form action="{{ route('collabs.store', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-person-plus" aria-hidden="true"></i>
                                Send a collab request
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        @endif

        <dl class="divide-y divide-line border-t border-line">
            @if ($user->major)
                <x-ui.fact label="Major">{{ $user->major }}</x-ui.fact>
            @endif
            @if ($user->year_of_enrollment)
                <x-ui.fact label="Joined UC Online">{{ $user->year_of_enrollment }}</x-ui.fact>
            @endif
            <x-ui.fact label="Focus" class="capitalize">{{ $user->focus_label }}</x-ui.fact>
        </dl>

        <x-people.contacts :person="$user" />
        <x-people.skills :skills="$user->skills" />
    </section>

    @include('users.partials.show-certificates')

    @include('users.partials.show-activities')
</div>
