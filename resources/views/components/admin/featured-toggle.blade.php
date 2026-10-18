{{-- --- Star button that features or unfeatures a record; disabled while the record is hidden; the class goes on the form --- --}}
@props(['action', 'visible', 'featured', 'tipClass' => ''])

<form action="{{ $action }}" method="POST" {{ $attributes }}>
    @csrf
    <button type="submit" @disabled(!$visible)
            @class([
                'group relative inline-flex h-6 w-6 items-center justify-center rounded-full border transition-all',
                'bg-gray-50 border-gray-100 text-gray-200 cursor-not-allowed' => !$visible,
                'bg-uco-yellow-400 border-uco-yellow-500 text-white hover:bg-uco-yellow-500' =>
                    $visible && $featured,
                'bg-white border-gray-200 text-gray-600 hover:text-uco-yellow-400 hover:border-uco-yellow-300' =>
                    $visible && !$featured,
            ])>
        <i class="bi bi-star-fill text-xs"></i>
        <x-ui.tooltip class="{{ $tipClass }}">{{ !$visible ? 'Need visible' : ($featured ? 'Unfeature' : 'Feature') }}</x-ui.tooltip>
    </button>
</form>
