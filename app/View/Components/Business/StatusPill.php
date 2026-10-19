<?php

namespace App\View\Components\Business;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Status badge on a My Business card: approved, rejected, or waiting (pending / needs revision).
 */
class StatusPill extends Component
{
    // --- Colour and icon per status (anything else counts as waiting) ---

    private const LOOKS = [
        'approved' => ['badge-success', 'bi-check-circle'],
        'rejected' => ['badge-danger', 'bi-x-circle'],
        'waiting' => ['badge-warning', 'bi-hourglass-split'],
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
