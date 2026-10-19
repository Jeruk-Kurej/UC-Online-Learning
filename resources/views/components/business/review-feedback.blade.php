{{-- --- The admin's reason under a rejected or needs-revision item (App\View\Components\Business\ReviewFeedback picks the colours) --- --}}
<div class="{{ $boxClass }} rounded-md border p-3">
    <p class="{{ $titleClass }} text-sm font-semibold">{{ $title }}</p>
    <p class="{{ $textClass }} mt-1 text-base">{{ $item->rejection_reason }}</p>
</div>
