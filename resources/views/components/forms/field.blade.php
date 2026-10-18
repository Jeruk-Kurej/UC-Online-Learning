{{-- --- Label, the control from the slot, and the field's validation message --- --}}
<div {{ $wrapperAttributes() }}>
    <label for="{{ $for }}" class="field-label">{{ $label }}@if ($required)
            <span class="text-red-700" aria-hidden="true">*</span><span class="sr-only">(required)</span>
        @endif
    </label>
    {{ $slot }}
    @if ($showError)
        @error($name)
            <p class="field-error">{{ $message }}</p>
        @enderror
    @endif
</div>
