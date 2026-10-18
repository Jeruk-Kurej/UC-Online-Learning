{{-- --- One way to reach a person: icon and value, the label is read out for screen readers --- --}}
@props(['href', 'icon', 'label', 'newTab' => false])

<a href="{{ $href }}" @if ($newTab) target="_blank" rel="noopener" @endif class="flex items-center gap-3 text-base text-gray-800 hover:text-brand-800 hover:underline">
    <i class="bi {{ $icon }} text-lg text-brand-700" aria-hidden="true"></i>
    <span class="break-all"><span class="sr-only">{{ $label }}: </span>{{ $slot }}</span>
</a>
