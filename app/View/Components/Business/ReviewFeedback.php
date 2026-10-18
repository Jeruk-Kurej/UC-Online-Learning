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
        'rejected' => ['border-red-300 bg-red-50', 'text-red-900', 'text-red-900', 'Why it was rejected'],
        'need_revision' => ['border-amber-300 bg-amber-50', 'text-amber-900', 'text-amber-900', 'What to change'],
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
