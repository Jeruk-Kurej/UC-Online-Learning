{{-- --- Bottom row of a form: secondary actions first, the main button last; stacked on phones --- --}}
<div {{ $attributes->class(['flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end']) }}>
    {{ $slot }}
</div>
