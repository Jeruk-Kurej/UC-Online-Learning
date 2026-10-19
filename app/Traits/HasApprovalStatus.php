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
            'pending' => 'Pending approval',
            'rejected' => 'Rejected',
            'need_revision' => 'Needs revision',
            default => 'Pending approval',
        };
    }

    // --- Scopes ---

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true)
            ->where('approval_status', 'approved')
            ->where(fn ($q) => $this->whereSomeoneVisible($q));
    }

    // --- People ---

    protected function whereSomeoneVisible($query): void
    {
        $query->whereHas('user', fn ($q) => $q->where('is_visible', true));
    }

    public function hasVisiblePeople(): bool
    {
        return (bool) $this->user?->is_visible;
    }

    /**
     * The same rule as the visible scope, for one record: anyone may open it; otherwise only the people who manage it.
     */
    public function isPublic(): bool
    {
        return $this->is_visible && $this->approval_status === 'approved' && $this->hasVisiblePeople();
    }

    // --- Admin decision: only approved items are visible; a reason is kept for rejections and revision requests ---

    public function hasReviewFeedback(): bool
    {
        return in_array($this->status, ['rejected', 'need_revision']) && $this->rejection_reason;
    }

    public function applyApprovalStatus(string $status, ?string $reason): void
    {
        $this->fill([
            'approval_status' => $status,
            'rejection_reason' => in_array($status, ['rejected', 'need_revision']) ? $reason : null,
            'is_visible' => $status === 'approved',
        ]);
        $this->save();
    }
}
