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
    // --- Wrapper (only when the message is shown), label and message classes per form ---

    private const LOOKS = [
        'business' => [
            'wrapper' => 'relative pb-5',
            'label' => 'form-label-custom',
            'message' => 'absolute bottom-0 left-0 text-[10px] font-bold uppercase tracking-tight text-red-600',
        ],
        'work-profile' => ['wrapper' => '', 'label' => 'form-label-custom', 'message' => 'mt-1 text-xs font-medium text-red-600'],
        'user' => ['wrapper' => '', 'label' => 'form-label-custom', 'message' => 'mt-1 text-sm text-red-600'],
        'login' => ['wrapper' => '', 'label' => 'form-label-custom', 'message' => 'mt-2 text-xs font-medium text-red-600'],
        'offering' => ['wrapper' => '', 'label' => 'mb-2 block text-sm font-medium text-gray-700', 'message' => 'mt-1 text-sm text-red-600'],
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
