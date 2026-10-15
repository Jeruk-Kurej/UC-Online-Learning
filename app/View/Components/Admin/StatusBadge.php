<?php

namespace App\View\Components\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Approval status of a business or work profile in an admin list, coloured per status.
 */
class StatusBadge extends Component
{
    // --- Colours per status ---

    private const TONES = [
        'approved' => 'bg-green-100 text-green-700',
        'pending' => 'bg-amber-100 text-amber-700',
        'rejected' => 'bg-red-100 text-red-700',
        'need_revision' => 'bg-blue-100 text-blue-700',
    ];

    public string $toneClass;

    public function __construct(public Model $item)
    {
        $this->toneClass = self::TONES[$item->status] ?? 'bg-gray-100 text-gray-700';
    }

    public function render(): View
    {
        return view('components.admin.status-badge');
    }
}
