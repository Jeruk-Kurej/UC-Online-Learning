<?php

namespace App\View\Components\Business;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Badge on a business page saying whether it sells products, services or both.
 */
class ModeBadge extends Component
{
    // --- Labels per mode (anything else reads as a service business) ---

    private const MODES = [
        'both' => 'Products and services',
        'product' => 'Products',
        'service' => 'Services',
    ];

    public string $label;

    public function __construct(public string $mode)
    {
        $this->label = self::MODES[$mode] ?? self::MODES['service'];
    }

    public function render(): View
    {
        return view('components.business.mode-badge');
    }
}
