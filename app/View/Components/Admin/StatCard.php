<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * One number on an admin page's statistics row: a coloured label over a big value.
 */
class StatCard extends Component
{
    // --- Label and value colours per tone ---

    private const TONES = [
        'gray' => ['label' => 'text-gray-600', 'value' => 'text-gray-900'],
        'blue' => ['label' => 'text-blue-500', 'value' => 'text-blue-600'],
        'green' => ['label' => 'text-green-500', 'value' => 'text-green-600'],
        'amber' => ['label' => 'text-amber-500', 'value' => 'text-amber-600'],
        'red' => ['label' => 'text-red-500', 'value' => 'text-red-600'],
        'purple' => ['label' => 'text-purple-500', 'value' => 'text-purple-600'],
    ];

    public string $labelClass;

    public string $valueClass;

    public function __construct(public string $tone = 'gray', public bool $compactOnMobile = true, public ?string $valueId = null)
    {
        ['label' => $this->labelClass, 'value' => $this->valueClass] = self::TONES[$tone];
    }

    public function render(): View
    {
        return view('components.admin.stat-card');
    }
}
