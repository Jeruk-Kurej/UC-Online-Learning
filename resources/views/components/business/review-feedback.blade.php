{{-- --- Reason box (App\View\Components\Business\ReviewFeedback picks red for rejected, blue for needs revision) --- --}}
<div class="{{ $boxClass }} mb-4 rounded-xl border p-3 text-left">
    <p class="{{ $titleClass }} mb-1 text-xs font-bold">
        {{ $title }}
    </p>
    <p class="{{ $textClass }} text-xs italic">"{{ $item->rejection_reason }}"</p>
</div>
