{{-- --- Heading of a card or number list in the About builder: label with the live count, and the add button --- --}}
@props(['label', 'count', 'add', 'button'])

<div class="flex flex-wrap items-center justify-between gap-3">
    <h3 class="text-base font-semibold text-gray-900">{{ $label }} (<span x-text="{{ $count }}"></span>)</h3>
    <button type="button" @click="{{ $add }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-plus-lg" aria-hidden="true"></i>
        {{ $button }}
    </button>
</div>
