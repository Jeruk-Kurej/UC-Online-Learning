{{-- --- Search box of a filterable list: typing reloads the list (ajaxList), Enter reloads at once; the class goes on the wrapper --- --}}
<div {{ $attributes->only('class')->class(['relative']) }}>
    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
        <i class="bi bi-search text-gray-400"></i>
    </div>
    <input type="text" name="search" @input="submitDebounced()" @keydown.enter.prevent="updateList()"
           {{ $attributes->except('class')->class(['w-full rounded-md border-gray-300 bg-white py-2 pl-10 pr-4 text-sm shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500']) }}>
</div>
