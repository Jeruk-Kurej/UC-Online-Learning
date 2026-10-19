{{-- --- Search box of a filterable list, a pill with a magnifier (the home hero has its own glass version on the photo): typing reloads the list (ajaxList), Enter reloads at once; the class goes on the wrapper --- --}}
<div {{ $attributes->only('class')->class(['relative']) }}>
    <label for="{{ $attributes->get('id', 'search') }}" class="sr-only">Search</label>
    <i class="bi bi-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-600" aria-hidden="true"></i>
    <input type="search" name="search" @input="submitDebounced()" @keydown.enter.prevent="updateList()" {{ $attributes->except('class')->class(['field-input !rounded-full !pl-11 [&::-webkit-search-cancel-button]:hidden']) }}>
</div>
