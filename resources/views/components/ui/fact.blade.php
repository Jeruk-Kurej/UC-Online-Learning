{{-- --- One label and value row of a facts list (put rows inside <dl class="divide-y divide-line">) --- --}}
@props(['label'])

<div class="flex gap-4 py-3">
    <dt class="w-32 shrink-0 text-sm text-gray-600">{{ $label }}</dt>
    <dd {{ $attributes->class(['min-w-0 flex-1 break-words text-base text-gray-900']) }}>{{ $slot }}</dd>
</div>
