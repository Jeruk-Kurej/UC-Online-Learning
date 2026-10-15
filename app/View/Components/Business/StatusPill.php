<?php

namespace App\View\Components\Business;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Solid status pill on a My Business card: approved, rejected, or waiting (pending / needs revision).
 */
class StatusPill extends Component
{
    // --- Colour and icon per status (anything else counts as waiting) ---

    private const LOOKS = [
        'approved' => ['bg-green-500 text-white', 'bi-check-circle-fill'],
        'rejected' => ['bg-red-500 text-white', 'bi-x-circle-fill'],
        'waiting' => ['bg-uco-orange-500 text-white', 'bi-hourglass-split'],
    ];

    public string $colorClass;

    public string $icon;

    public function __construct(public Model $item)
    {
        [$this->colorClass, $this->icon] = self::LOOKS[$item->status] ?? self::LOOKS['waiting'];
    }

    public function render(): View
    {
        return view('components.business.status-pill');
    }
}
