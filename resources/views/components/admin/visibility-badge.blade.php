{{-- --- "Visible" or "Hidden" for a record --- --}}
@props(['visible'])

<span {{ $attributes->class(['badge', $visible ? 'badge-success' : 'badge-neutral']) }}>{{ $visible ? 'Visible' : 'Hidden' }}</span>
