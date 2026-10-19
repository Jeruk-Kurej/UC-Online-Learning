<?php

namespace App\Http\Controllers;

use App\Models\Collab;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CollabController extends Controller
{
    public function store(User $user)
    {
        $sender = Auth::user();

        if ($sender->id === $user->id) {
            return back()->with('error', 'You cannot collab with yourself.');
        }

        abort_unless($sender->canCollabWith($user), 403);

        $existing = Collab::between($sender->id, $user->id)->first();

        if ($existing) {
            if ($existing->status === 'rejected') {
                // --- A rejected request can be sent again by reusing its row ---
                $existing->update([
                    'sender_id' => $sender->id,
                    'recipient_id' => $user->id,
                    'status' => 'pending',
                ]);
            } else {
                return back()->with('error', 'A collab request already exists or you are already connected.');
            }
        } else {
            Collab::create([
                'sender_id' => $sender->id,
                'recipient_id' => $user->id,
                'status' => 'pending',
            ]);
        }

        Message::create([
            'sender_id' => $sender->id,
            'recipient_id' => $user->id,
            'subject' => 'Collab Request from '.$sender->name,
            'body' => $sender->name.' would like to connect and collab with you on UC Online Learning.',
            'type' => 'collab_invite',
        ]);

        return back()->with('success', 'Collab request sent successfully!');
    }

    public function accept(Collab $collab)
    {
        $user = Auth::user();

        if ($notPending = $this->guardAnswer($collab, $user)) {
            return $notPending;
        }

        $collab->update(['status' => 'accepted']);

        Message::create([
            'sender_id' => $user->id,
            'recipient_id' => $collab->sender_id,
            'subject' => 'Collab Request Accepted',
            'body' => $user->name.' has accepted your collab request! You are now connected.',
            'type' => 'collab_accepted',
        ]);

        return back()->with('success', 'You are now connected with '.$collab->sender->name.'!');
    }

    public function reject(Collab $collab)
    {
        $user = Auth::user();

        if ($notPending = $this->guardAnswer($collab, $user)) {
            return $notPending;
        }

        $collab->update(['status' => 'rejected']);

        return back()->with('success', 'Collab request rejected.');
    }

    // --- Answering a request ---

    /**
     * Only the recipient may answer, and only while the request is pending; returns the redirect to send otherwise.
     */
    private function guardAnswer(Collab $collab, User $user): ?RedirectResponse
    {
        if ($collab->recipient_id !== $user->id) {
            abort(403);
        }

        return $collab->status === 'pending'
            ? null
            : back()->with('error', 'This request is no longer pending.');
    }
}
