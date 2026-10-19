{{-- --- Secondary button that leads back to the previous page --- --}}
@props(['href', 'label'])

<a href="{{ $href }}" class="btn btn-secondary btn-sm">
    <i class="bi bi-arrow-left" aria-hidden="true"></i>
    <span>{{ $label }}</span>
</a>
