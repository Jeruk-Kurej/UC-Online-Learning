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
        'bar' => ['inline-flex h-16 items-center border-b-4 px-1 text-base font-medium', 'border-brand-500 font-semibold text-brand-900', 'border-transparent text-gray-700 hover:border-brand-200 hover:text-brand-900'],
        'menu' => ['block rounded-md px-3 py-3 text-base font-medium', 'bg-brand-50 text-brand-900', 'text-gray-800 hover:bg-gray-100'],
        'account' => ['block rounded-md px-3 py-3 text-base', 'bg-brand-50 font-medium text-brand-900', 'text-gray-800 hover:bg-gray-100'],
        'dropdown' => ['block px-4 py-2.5 text-base text-gray-800 hover:bg-brand-50 hover:text-brand-900', '', ''],
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
