<?php

namespace App\View\Components\Flash;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * One session flash message (success, error, status or warning) shown as a toast that hides itself.
 */
class Message extends Component
{
    // --- Look per type ---

    private const STYLES = [
        'success' => ['class' => 'toast-success', 'icon' => 'bi-check-circle-fill', 'iconColor' => 'text-green-700'],
        'error' => ['class' => 'toast-error', 'icon' => 'bi-exclamation-triangle-fill', 'iconColor' => 'text-red-700'],
        'status' => ['class' => 'toast-info', 'icon' => 'bi-info-circle-fill', 'iconColor' => 'text-brand-600'],
        'warning' => ['class' => 'toast-warning', 'icon' => 'bi-exclamation-circle-fill', 'iconColor' => 'text-amber-600'],
    ];

    public string $class;

    public string $icon;

    public string $iconColor;

    public function __construct(public string $type, public string $message)
    {
        ['class' => $this->class, 'icon' => $this->icon, 'iconColor' => $this->iconColor] = self::STYLES[$type];
    }

    public function render(): View
    {
        return view('components.flash.message');
    }
}
