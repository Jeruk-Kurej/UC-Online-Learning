<?php

namespace App\Traits;

trait HasApprovalStatus
{
    // --- Accessors ---

    public function getStatusAttribute(): string
    {
        return $this->approval_status ?? ($this->is_visible ? 'approved' : 'pending');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'Approved',
            'pending' => 'Pending Approval',
            'rejected' => 'Rejected',
            'need_revision' => 'Need Revision',
            default => 'Pending Approval',
        };
    }

    // --- Scopes ---

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true)
            ->where('approval_status', 'approved')
            ->whereHas('user', fn ($q) => $q->where('is_visible', true));
    }
}
