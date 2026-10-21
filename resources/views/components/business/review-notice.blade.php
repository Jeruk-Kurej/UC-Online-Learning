{{-- --- Why visitors cannot see this page yet (App\View\Components\Business\ReviewNotice picks the words and colours) --- --}}
<div {{ $attributes->class([$boxClass, 'rounded-lg border p-4']) }} role="status">
    <p class="{{ $textClass }} text-base font-semibold">{{ $title }}</p>
    <p class="{{ $textClass }} mt-1 text-base">{{ $text }}</p>
    @if ($reason)
        <p class="{{ $textClass }} mt-3 text-base"><span class="font-semibold">Message from the admin team:</span> {{ $reason }}</p>
    @endif
</div>
