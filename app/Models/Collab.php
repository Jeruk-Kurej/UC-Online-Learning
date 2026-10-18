<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collab extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'status',
    ];

    /**
     * Get the user who sent the collab request.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the user who received the collab request.
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Requests between two users, whoever sent them.
     */
    public function scopeBetween(Builder $query, int $firstId, int $secondId): Builder
    {
        return $query->where(function ($pair) use ($firstId, $secondId) {
            $pair->where(fn ($q) => $q->where('sender_id', $firstId)->where('recipient_id', $secondId))
                ->orWhere(fn ($q) => $q->where('sender_id', $secondId)->where('recipient_id', $firstId));
        });
    }

    /**
     * The other person in this connection, seen from the given user.
     */
    public function partnerFor(User $user): ?User
    {
        return $this->sender_id === $user->id ? $this->recipient : $this->sender;
    }
}
