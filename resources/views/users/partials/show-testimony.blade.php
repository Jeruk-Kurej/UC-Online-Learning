{{-- --- Profile page testimony, with an edit link for the person themselves --- --}}
@if ($user->testimony)
    <x-ui.section title="Testimony" :href="auth()->id() === $user->id ? route('uc-testimonies.my') : null" link-label="Edit my testimony">
        <figure class="card">
            <blockquote class="whitespace-pre-line text-lg text-gray-800">&ldquo;{{ $user->testimony }}&rdquo;</blockquote>
            <figcaption class="mt-4 text-sm text-gray-600">{{ $user->name }}</figcaption>
        </figure>
    </x-ui.section>
@elseif (auth()->id() === $user->id)
    <x-ui.empty-state title="You have not shared a testimony yet">
        Tell others what UC Online Learning has meant for you.
        <x-slot:action><a href="{{ route('uc-testimonies.my') }}" class="btn btn-primary">Write my testimony</a></x-slot:action>
    </x-ui.empty-state>
@endif
