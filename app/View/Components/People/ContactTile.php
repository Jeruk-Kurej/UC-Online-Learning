<?php

namespace App\View\Components\People;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * One contact link in a profile card (round icon, label, value, arrow) that tints on hover.
 */
class ContactTile extends Component
{
    // --- Icon circle, value and arrow tint per tone ---

    private const TONES = [
        'green' => ['bg-green-50 text-green-500 group-hover:bg-green-100', 'group-hover:text-green-600', 'group-hover:text-green-500'],
        'blue' => ['bg-blue-50 text-blue-500 group-hover:bg-blue-100', 'group-hover:text-blue-600', 'group-hover:text-blue-500'],
        'indigo' => ['bg-indigo-50 text-indigo-500 group-hover:bg-indigo-100', 'group-hover:text-indigo-600', 'group-hover:text-indigo-500'],
    ];

    public string $circleClass;

    public string $valueClass;

    public string $arrowClass;

    public function __construct(public string $href, public string $icon, public string $label, string $tone, public bool $newTab = false)
    {
        [$this->circleClass, $this->valueClass, $this->arrowClass] = self::TONES[$tone];
    }

    public function render(): View
    {
        return view('components.people.contact-tile');
    }
}
