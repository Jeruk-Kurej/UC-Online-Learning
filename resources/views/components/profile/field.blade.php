@props(['label', 'name', 'value' => null, 'type' => 'text', 'compact' => false])

<div>
    <label class="{{ $compact ? 'profile-sublabel' : 'profile-label' }}">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}" {{ $attributes->merge(['class' => 'profile-input']) }}>
</div>
