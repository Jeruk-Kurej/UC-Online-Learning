{{-- --- Heading of a card or stat list in the About builder: label with the live count, and the add button --- --}}
@props(['label', 'count', 'add', 'button'])

<div class="flex items-center justify-between">
    <span class="text-xs font-bold uppercase tracking-wider text-gray-500">{{ $label }} (<span x-text="{{ $count }}"></span>)</span>
    <button type="button" @click="{{ $add }}" class="inline-flex items-center gap-1.5 rounded-lg border border-uco-orange-200 bg-uco-orange-50 px-3.5 py-1.5 text-xs font-bold text-uco-orange-700 transition-colors hover:bg-uco-orange-100">
        <i class="bi bi-plus-circle"></i> {{ $button }}
    </button>
</div>
