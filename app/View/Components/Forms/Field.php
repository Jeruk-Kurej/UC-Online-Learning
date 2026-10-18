<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\View;

/**
 * One form field: its label, the control (the slot) and its validation message.
 */
class Field extends Component
{
    public function __construct(
        public string $name,
        public string $label,
        public bool $required = false,
        public ?string $for = null,
        public bool $showError = true,
    ) {
        $this->for ??= $name;
    }

    public function wrapperAttributes(): ComponentAttributeBag
    {
        return $this->attributes->filter(fn ($value) => $value !== '');
    }

    public function render(): View
    {
        return view('components.forms.field');
    }
}
