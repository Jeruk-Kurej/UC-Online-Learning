<?php

namespace App\View\Components\Ui;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Logo or photo in a fixed frame; the first letter of the name sits behind the image and shows when the file is missing or fails to load.
 */
class Thumb extends Component
{
    public string $initial;

    public string $shapeClass;

    public function __construct(public ?string $src = null, string $name = '', public string $size = 'h-12 w-12', bool $round = false)
    {
        $this->initial = mb_strtoupper(mb_substr(trim($name), 0, 1));
        $this->shapeClass = $round ? 'rounded-full' : 'rounded-md';
    }

    public function render(): View
    {
        return view('components.ui.thumb');
    }
}
