{{-- --- Hidden multi-file input and its "Add files" button, inside x-data="activityFiles(...)" --- --}}
@props(['name'])

<div {{ $attributes->class(['relative']) }}>
    <input type="file" name="{{ $name }}[]" id="{{ $name }}" multiple accept="image/*,application/pdf" class="hidden" x-ref="activityInput" @change="handleNewFiles($event)">
    <button type="button" @click="pickFiles()" class="btn btn-secondary">
        <i class="bi bi-plus-lg" aria-hidden="true"></i>
        Add files
    </button>
</div>
