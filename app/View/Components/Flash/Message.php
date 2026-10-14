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
        'success' => ['border' => 'border-slate-100/90', 'badge' => 'bg-emerald-50 text-emerald-600 ring-4 ring-emerald-500/10', 'icon' => 'bi-check-circle-fill'],
        'error' => ['border' => 'border-red-100/90', 'badge' => 'bg-red-50 text-red-600 ring-4 ring-red-500/10', 'icon' => 'bi-exclamation-triangle-fill'],
        'status' => ['border' => 'border-slate-100/90', 'badge' => 'bg-blue-50 text-blue-600 ring-4 ring-blue-500/10', 'icon' => 'bi-info-circle-fill'],
        'warning' => ['border' => 'border-slate-100/90', 'badge' => 'bg-yellow-50 text-yellow-600 ring-4 ring-yellow-500/10', 'icon' => 'bi-exclamation-circle-fill'],
    ];

    public string $border;

    public string $badge;

    public string $icon;

    public function __construct(public string $type, public string $message)
    {
        ['border' => $this->border, 'badge' => $this->badge, 'icon' => $this->icon] = self::STYLES[$type];
    }

    public function render(): View
    {
        return view('components.flash.message');
    }
}
