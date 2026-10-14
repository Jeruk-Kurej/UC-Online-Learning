<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Small square icon button (or link) in an admin list row, with a tooltip above it.
 */
class IconAction extends Component
{
    // --- Background and hover colour per variant ---

    private const VARIANTS = [
        'success' => 'bg-[#198754] hover:bg-[#157347]',
        'secondary' => 'bg-[#6c757d] hover:bg-[#5c636a]',
        'danger' => 'bg-[#dc3545] hover:bg-[#bb2d3b]',
    ];

    public string $colorClass;

    public function __construct(
        public string $icon,
        public string $tip,
        public string $variant = 'success',
        public ?string $href = null,
        public string $tipAlign = 'center',
        public string $arrowClass = '',
    ) {
        $this->colorClass = self::VARIANTS[$variant];
    }

    public function render(): View
    {
        return view('components.admin.icon-action');
    }
}
