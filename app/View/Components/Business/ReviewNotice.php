<?php

namespace App\View\Components\Business;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Tells the people who manage a business or work profile why visitors cannot see it yet, and what to do about it.
 */
class ReviewNotice extends Component
{
    // --- Box, text classes, title and explanation per status (":noun" is "business" or "work profile") ---

    private const LOOKS = [
        'pending' => ['border-amber-300 bg-amber-50', 'text-amber-900', 'Waiting for approval', 'Only you and the admin team can see this :noun. It appears in the directory once the admin team approves it.'],
        'need_revision' => ['border-amber-300 bg-amber-50', 'text-amber-900', 'Changes requested', 'The admin team asked for changes. Edit the :noun and save it to send it back for review.'],
        'rejected' => ['border-red-300 bg-red-50', 'text-red-900', 'Not approved', 'The admin team did not approve this :noun, so only you and the admin team can see it.'],
        'hidden' => ['border-line bg-white', 'text-gray-900', 'Hidden from visitors', 'Only you and the admin team can see this :noun right now.'],
    ];

    public string $boxClass;

    public string $textClass;

    public string $title;

    public string $text;

    public ?string $reason;

    public function __construct(public Model $item, string $noun)
    {
        $look = self::LOOKS[$item->status] ?? self::LOOKS['hidden'];
        [$this->boxClass, $this->textClass, $this->title, $text] = $item->status === 'approved' ? self::LOOKS['hidden'] : $look;
        $this->text = str_replace(':noun', $noun, $text);
        $this->reason = $item->hasReviewFeedback() ? $item->rejection_reason : null;
    }

    public function render(): View
    {
        return view('components.business.review-notice');
    }
}
