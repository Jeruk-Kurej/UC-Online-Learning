{{-- --- Form around the featured button of a list row; the class goes on the form --- --}}
@props(['action', 'visible', 'featured'])

<form action="{{ $action }}" method="POST" {{ $attributes }}>
    @csrf
    <x-admin.featured-button :visible="$visible" :featured="$featured" />
</form>
