<?php

namespace App\View\Components\Business;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * One fact in the insights row of a business or work-profile page: tinted icon, label (named slot), value (slot), caption.
 */
class Insight extends Component
{
    // --- Icon box colours per tone ---

    private const TONES = [
        'emerald' => 'border-emerald-100/50 bg-emerald-50 text-emerald-600',
        'blue' => 'border-blue-100/50 bg-blue-50 text-blue-600',
        'indigo' => 'border-indigo-100/50 bg-indigo-50 text-indigo-600',
        'orange' => 'border-orange-100/50 bg-orange-50 text-uco-orange-600',
    ];

    public string $toneClass;

    public function __construct(public string $icon, public string $tone, public string $caption, public bool $italicCaption = false)
    {
        $this->toneClass = self::TONES[$tone];
    }

    public function render(): View
    {
        return view('components.business.insight');
    }
}
