<?php

namespace App\View\Components\Cms;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * One field of the About page builder bound to Alpine with x-model: a text input, a textarea (rows) or a select (options).
 */
class Field extends Component
{
    // --- Control size, weight and colour per look; "item-" looks sit inside a card or stat box ---

    private const LOOKS = [
        'heading' => 'px-4 py-2.5 text-sm font-semibold text-gray-900',
        'body' => 'px-4 py-2.5 text-sm font-medium text-gray-700',
        'item-heading' => 'px-3 py-2 text-xs font-semibold text-gray-900',
        'item-body' => 'px-3 py-2 text-xs font-medium text-gray-700',
        'item-label' => 'px-3 py-2 text-xs font-semibold text-gray-800',
        'item-number' => 'px-3 py-2 text-sm font-bold text-uco-orange-600',
    ];

    public string $wrapperClass;

    public string $labelClass;

    public string $controlClass;

    public function __construct(
        public string $label,
        public string $model,
        string $look = 'heading',
        public ?int $rows = null,
        public array $options = [],
    ) {
        $inItem = str_starts_with($look, 'item-');
        $this->wrapperClass = $inItem ? 'space-y-1' : 'space-y-1.5';
        $this->labelClass = $inItem
            ? 'block text-xs font-bold   text-gray-600'
            : 'block text-xs font-bold   text-gray-700';
        $this->controlClass = 'w-full rounded-lg border border-gray-200 focus:border-uco-orange-500 focus:ring-uco-orange-500 '.self::LOOKS[$look];
    }

    public function render(): View
    {
        return view('components.cms.field');
    }
}
