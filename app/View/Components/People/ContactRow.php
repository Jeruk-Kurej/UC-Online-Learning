<?php

namespace App\View\Components\People;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * One contact link in the profile card (WhatsApp, email, LinkedIn) that tints on hover.
 */
class ContactRow extends Component
{
    // --- Hover tint per tone: row, label, icon, value ---

    private const TONES = [
        'green' => ['hover:border-green-100/50 hover:bg-green-50/50', 'group-hover:text-green-600', 'text-green-600', 'group-hover:text-green-700'],
        'blue' => ['hover:border-blue-100/50 hover:bg-blue-50/50', 'group-hover:text-blue-600', 'text-blue-600', 'group-hover:text-blue-700'],
        'indigo' => ['hover:border-indigo-100/50 hover:bg-indigo-50/50', 'group-hover:text-indigo-600', 'text-indigo-600', 'group-hover:text-indigo-700'],
    ];

    public string $rowClass;

    public string $labelClass;

    public string $iconClass;

    public string $valueClass;

    public function __construct(public string $href, public string $icon, public string $label, string $tone, public bool $newTab = false)
    {
        [$this->rowClass, $this->labelClass, $this->iconClass, $this->valueClass] = self::TONES[$tone];
    }

    public function render(): View
    {
        return view('components.people.contact-row');
    }
}
