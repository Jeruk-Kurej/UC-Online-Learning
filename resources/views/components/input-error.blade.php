{{-- --- Input error --- --}}
@props(['messages'])

@if ($messages)
    <ul {{ $attributes->class(['field-error']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
