{{-- --- Reset button of a filter bar, swapped for an "Updating..." badge while the list reloads ($loading names the Alpine flag) --- --}}
@props(['loading' => 'isSubmitting'])

<button type="button" @click="resetFilters()" x-show="!{{ $loading }}" title="Reset Filters"
        {{ $attributes->class(['inline-flex h-[38px] w-[38px] items-center justify-center rounded-md border border-gray-300 bg-white text-gray-600 shadow-sm transition hover:bg-gray-50 hover:text-gray-900']) }}>
    <i class="bi bi-arrow-clockwise text-lg"></i>
</button>
<div x-show="{{ $loading }}" x-cloak class="inline-flex h-[38px] items-center justify-center rounded-md border border-uco-orange-200 bg-uco-orange-50 px-3 text-uco-orange-700 shadow-sm">
    <x-ui.spinner />
    <span class="ml-2 hidden text-xs font-medium sm:inline">Updating...</span>
</div>
