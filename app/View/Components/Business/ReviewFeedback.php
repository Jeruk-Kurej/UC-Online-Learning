<?php

namespace App\View\Components\Business;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * The admin's reason under a rejected or needs-revision item.
 */
class ReviewFeedback extends Component
{
    // --- Box, title and text classes plus title per outcome ---

    private const LOOKS = [
        'rejected' => ['bg-red-50 border-red-100', 'text-red-600', 'text-red-700', 'Rejection Reason:'],
        'need_revision' => ['bg-blue-50 border-blue-100', 'text-blue-600', 'text-blue-700', 'Revision Feedback:'],
    ];

    public string $boxClass;

    public string $titleClass;

    public string $textClass;

    public string $title;

    public function __construct(public Model $item)
    {
        [$this->boxClass, $this->titleClass, $this->textClass, $this->title] = self::LOOKS[$item->status === 'rejected' ? 'rejected' : 'need_revision'];
    }

    public function render(): View
    {
        return view('components.business.review-feedback');
    }
}
