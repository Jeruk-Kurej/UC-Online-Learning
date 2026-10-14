@props(['label', 'value'])

<div {{ $attributes }}>
    <label class="profile-label">{{ $label }}</label>
    <div class="profile-readonly">{{ $value }}</div>
</div>
