<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Collab;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollabController extends Controller
{
    /**
     * Send a Collab request to another user.
     */
    public function store(User $user)
    {
        $sender = Auth::user();

        // Prevent self-collab
        if ($sender->id === $user->id) {
            return back()->with('error', 'You cannot collab with yourself.');
        }

        // Check if already collabed or pending
        $existing = Collab::where(function ($q) use ($sender, $user) {
            $q->where('sender_id', $sender->id)->where('recipient_id', $user->id);
        })->orWhere(function ($q) use ($sender, $user) {
            $q->where('sender_id', $user->id)->where('recipient_id', $sender->id);
        })->first();

        if ($existing) {
            if ($existing->status === 'rejected') {
                // If it was rejected, we can allow them to send a new request by updating the old one
                $existing->update([
                    'sender_id' => $sender->id,
                    'recipient_id' => $user->id,
                    'status' => 'pending',
                ]);
            } else {
                return back()->with('error', 'A collab request already exists or you are already connected.');
            }
        } else {
            // Create the Collab record
            Collab::create([
                'sender_id' => $sender->id,
                'recipient_id' => $user->id,
                'status' => 'pending',
            ]);
        }

        // Send a message invite to their inbox
        Message::create([
            'sender_id' => $sender->id,
            'recipient_id' => $user->id,
            'subject' => 'Collab Request from ' . $sender->name,
            'body' => $sender->name . ' would like to connect and collab with you on UC Online Learning.',
            'type' => 'collab_invite',
        ]);

        return back()->with('success', 'Collab request sent successfully!');
    }

    /**
     * Accept a Collab request.
     */
    public function accept(Collab $collab)
    {
        $user = Auth::user();

        // Ensure the logged-in user is the recipient
        if ($collab->recipient_id !== $user->id) {
            abort(403);
        }

        if ($collab->status !== 'pending') {
            return back()->with('error', 'This request is no longer pending.');
        }

        $collab->update(['status' => 'accepted']);

        // Send a confirmation message back to the sender
        Message::create([
            'sender_id' => $user->id,
            'recipient_id' => $collab->sender_id,
            'subject' => 'Collab Request Accepted',
            'body' => $user->name . ' has accepted your collab request! You are now connected.',
            'type' => 'collab_accepted',
        ]);

        return back()->with('success', 'You are now connected with ' . $collab->sender->name . '!');
    }

    /**
     * Reject a Collab request.
     */
    public function reject(Collab $collab)
    {
        $user = Auth::user();

        // Ensure the logged-in user is the recipient
        if ($collab->recipient_id !== $user->id) {
            abort(403);
        }

        if ($collab->status !== 'pending') {
            return back()->with('error', 'This request is no longer pending.');
        }

        $collab->update(['status' => 'rejected']);

        return back()->with('success', 'Collab request rejected.');
    }
}
