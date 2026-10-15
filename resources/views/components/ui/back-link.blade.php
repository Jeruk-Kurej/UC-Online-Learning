{{-- --- Outlined "back" button with a left arrow that nudges on hover --- --}}
@props(['href', 'label'])

<a href="{{ $href }}"
   class="group inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-all duration-200 hover:border-gray-900 hover:bg-gray-900 hover:text-white hover:shadow-md">
    <i class="bi bi-arrow-left text-base transition-transform duration-200 group-hover:-translate-x-0.5"></i>
    <span>{{ $label }}</span>
</a>
