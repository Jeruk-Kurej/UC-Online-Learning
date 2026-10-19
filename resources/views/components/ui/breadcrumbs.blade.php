{{-- --- "Directory > current page" trail above a detail page --- --}}
@props(['href', 'current'])

<nav {{ $attributes->class(['text-sm']) }} aria-label="Breadcrumb">
    <ol class="flex flex-wrap items-center gap-2 text-gray-600">
        <li><a href="{{ $href }}" class="text-link">Directory</a></li>
        <li aria-hidden="true"><i class="bi bi-chevron-right text-xs"></i></li>
        <li aria-current="page" class="font-medium text-gray-900">{{ $current }}</li>
    </ol>
</nav>
