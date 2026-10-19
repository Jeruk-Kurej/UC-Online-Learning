{{-- --- Button that shows and changes whether a record is featured on the home page; lib/featured-button.js redraws it --- --}}
@props(['visible', 'featured', 'type' => 'submit'])

<button type="{{ $type }}" data-featured-button aria-pressed="{{ $featured ? 'true' : 'false' }}" @disabled(!$visible) @unless ($visible) title="Only visible records can be featured" @endunless
        {{ $attributes->class(['btn btn-sm', $featured ? 'btn-primary' : 'btn-secondary']) }}>
    <i class="bi {{ $featured ? 'bi-star-fill' : 'bi-star' }}" aria-hidden="true"></i>
    <span data-featured-label>{{ $featured ? 'Featured' : 'Feature' }}</span>
</button>
