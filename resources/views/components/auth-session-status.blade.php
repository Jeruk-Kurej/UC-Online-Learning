{{-- --- Auth session status --- --}}
@props(['status'])

@if ($status)
    <div {{ $attributes->class(['text-base font-medium text-green-800']) }}>
        {{ $status }}
    </div>
@endif
