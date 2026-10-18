<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\View;

/**
 * One form field: its label, the control (the slot) and its validation message, styled per form.
 */
class Field extends Component
{
    // --- Wrapper, label and message classes per form (one look for all of them now) ---

    private const LOOKS = [
        'business' => ['wrapper' => '', 'label' => 'field-label', 'message' => 'field-error'],
        'work-profile' => ['wrapper' => '', 'label' => 'field-label', 'message' => 'field-error'],
        'user' => ['wrapper' => '', 'label' => 'field-label', 'message' => 'field-error'],
        'login' => ['wrapper' => '', 'label' => 'field-label', 'message' => 'field-error'],
        'offering' => ['wrapper' => '', 'label' => 'field-label', 'message' => 'field-error'],
    ];

    public string $labelClass;

    public string $messageClass;

    private string $wrapperClass;

    public function __construct(
        public string $name,
        public string $label,
        public string $look,
        public bool $required = false,
        public ?string $for = null,
        public bool $showError = true,
    ) {
        ['wrapper' => $this->wrapperClass, 'label' => $this->labelClass, 'message' => $this->messageClass] = self::LOOKS[$look];
        $this->for ??= $name;
    }

    public function wrapperAttributes(): ComponentAttributeBag
    {
        return $this->attributes
            ->class([$this->wrapperClass => $this->showError])
            ->filter(fn ($value) => $value !== '');
    }

    public function render(): View
    {
        return view('components.forms.field');
    }
}
