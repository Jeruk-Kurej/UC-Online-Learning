<?php

namespace App\View\Components\Nav;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Link in the top navigation; $active highlights the current section.
 */
class Link extends Component
{
    // --- Classes per menu: always, when active, when not active ---

    private const LOOKS = [
        'bar' => ['text-sm font-bold', 'text-soft-gray-900 border-b-2 border-uco-orange-500', 'text-soft-gray-600 hover:text-soft-gray-900'],
        'menu' => ['block py-2.5 text-base font-bold', 'text-uco-orange-500', 'text-gray-800'],
        'account' => ['block py-2 text-sm font-bold', 'text-uco-orange-500', 'text-gray-700'],
        'dropdown' => ['block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50', '', ''],
    ];

    public string $linkClass;

    public function __construct(public string $href, string $look, bool $active = false)
    {
        [$always, $whenActive, $otherwise] = self::LOOKS[$look];
        $this->linkClass = trim(($active ? $whenActive : $otherwise).' '.$always);
    }

    public function render(): View
    {
        return view('components.nav.link');
    }
}
