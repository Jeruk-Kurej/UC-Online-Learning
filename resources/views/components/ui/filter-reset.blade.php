{{-- --- "Clear filters" button of a filter bar, swapped for an "Updating" note while the list reloads ($loading names the Alpine flag) --- --}}
@props(['loading' => 'isSubmitting'])

<button type="button" @click="resetFilters()" x-show="!{{ $loading }}" {{ $attributes->class(['btn btn-secondary']) }}>Clear filters</button>
<span x-show="{{ $loading }}" x-cloak role="status" class="inline-flex min-h-[2.75rem] items-center gap-2 px-2 text-base">
    <x-ui.spinner />
    <span>Updating</span>
</span>
