{{-- --- Hidden multi-file input and its "Add Files" button, inside x-data="activityFiles(...)" --- --}}
@props(['name'])

<div {{ $attributes->class(['relative']) }}>
    <input type="file" name="{{ $name }}[]" id="{{ $name }}" multiple accept="image/*,application/pdf"
           class="hidden" x-ref="activityInput"
           @change="handleNewFiles($event)">
    <button type="button" @click="pickFiles()"
            class="inline-flex cursor-pointer select-none items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-[10px] font-extrabold text-blue-600 shadow-sm transition hover:bg-blue-100 hover:text-blue-700">
        <i class="bi bi-plus-lg text-[9px]"></i>
        <span>Add Files</span>
    </button>
</div>
