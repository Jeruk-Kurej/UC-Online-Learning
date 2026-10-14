@props(['title'])

<div class="profile-section">
    @isset($actions)
        <div class="profile-section-head">
            <h2 class="profile-heading">{{ $title }}</h2>
            {{ $actions }}
        </div>
    @else
        <h2 class="profile-heading profile-section-title">{{ $title }}</h2>
    @endisset

    {{ $slot }}
</div>
