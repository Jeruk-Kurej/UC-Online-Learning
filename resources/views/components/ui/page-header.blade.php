{{-- --- Page title, one plain sentence under it and an optional row of actions on the right --- --}}
@props(['title', 'subtitle' => null])

<header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="page-title">{{ $title }}</h1>
        @if ($subtitle)
            <p class="page-lead">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</header>
