<?php

namespace App\View\Components\People;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * A business or workplace on a profile page: logo on the left, name, details and "View Details" on the right.
 */
class VentureCard extends Component
{
    // --- Look per list: owned businesses, businesses the person helps run, workplaces ---

    private const VARIANTS = [
        'owned' => ['border' => 'hover:border-orange-200', 'title' => 'font-bold text-gray-900', 'accent' => 'group-hover:text-orange-600', 'footer' => '', 'link' => ''],
        'member' => ['border' => 'hover:border-blue-200', 'title' => 'font-bold text-gray-950', 'accent' => 'group-hover:text-blue-600', 'footer' => '', 'link' => ''],
        'company' => ['border' => 'hover:border-orange-200', 'title' => 'font-bold text-gray-900', 'accent' => 'group-hover:text-orange-600', 'footer' => 'gap-3', 'link' => 'flex-shrink-0'],
    ];

    public array $look;

    public function __construct(
        public string $href,
        public string $name,
        public ?string $logo = null,
        public ?string $logoAlt = null,
        public string $variant = 'owned',
    ) {
        $this->look = self::VARIANTS[$variant];
    }

    public function render(): View
    {
        return view('components.people.venture-card');
    }
}
