{{-- --- "Directory › current page" trail above a detail page --- --}}
@props(['href', 'current', 'currentClass' => ''])

<nav {{ $attributes->class(['flex text-sm font-medium']) }} aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li>
            <a href="{{ $href }}" class="text-gray-600 transition hover:text-uco-orange-500">Directory</a>
        </li>
        <li class="flex items-center space-x-2">
            <svg class="h-4 w-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" />
            </svg>
            <span @class([$currentClass, 'text-gray-900'])>{{ $current }}</span>
        </li>
    </ol>
</nav>
