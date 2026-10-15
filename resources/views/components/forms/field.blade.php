{{-- --- Label, the control from the slot, and the field's validation message (App\View\Components\Forms\Field picks the classes) --- --}}
<div {{ $wrapperAttributes() }}>
    <label for="{{ $for }}" class="{{ $labelClass }}">{{ $label }}@if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    {{ $slot }}
    @if ($showError)
        @error($name)
            <p class="{{ $messageClass }}">{{ $message }}</p>
        @enderror
    @endif
</div>
