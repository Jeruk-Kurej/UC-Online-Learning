{{-- --- Search box of a filterable list: typing reloads the list (ajaxList), Enter reloads at once; the class goes on the wrapper --- --}}
<div {{ $attributes->only('class')->class(['relative']) }}>
    <label for="{{ $attributes->get('id', 'search') }}" class="sr-only">Search</label>
    <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-600" aria-hidden="true"></i>
    <input type="search" name="search" @input="submitDebounced()" @keydown.enter.prevent="updateList()" {{ $attributes->except('class')->class(['field-input !pl-10']) }}>
</div>
