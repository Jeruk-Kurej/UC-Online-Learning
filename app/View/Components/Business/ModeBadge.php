<?php

namespace App\View\Components\Business;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Badge on a business page saying whether it sells products, services or both.
 */
class ModeBadge extends Component
{
    // --- Colour and labels per mode (anything else reads as a service business) ---

    private const MODES = [
        'both' => ['tone' => 'bg-purple-100 text-purple-700', 'label' => 'Products & Services', 'short' => 'Both'],
        'product' => ['tone' => 'bg-blue-100 text-blue-700', 'label' => 'Product-Based', 'short' => 'Product'],
        'service' => ['tone' => 'bg-green-100 text-green-700', 'label' => 'Service-Based', 'short' => 'Service'],
    ];

    public string $toneClass;

    public string $label;

    public string $shortLabel;

    public function __construct(public string $mode)
    {
        ['tone' => $this->toneClass, 'label' => $this->label, 'short' => $this->shortLabel] = self::MODES[$mode] ?? self::MODES['service'];
    }

    public function render(): View
    {
        return view('components.business.mode-badge');
    }
}
