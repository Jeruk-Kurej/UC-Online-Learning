<?php

namespace App\Http\Controllers;

use App\Models\Collab;
use App\Models\Message;
use Illuminate\Http\Request;

class InboxController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if($user->isAdmin(), 403, 'Admin accounts have no inbox.');

        $messages = $user->receivedMessages()->newestFirst()->paginate(15);

        $sentCollabs = $user->sentCollabs()->with(['recipient'])->latest()->get();

        $connections = Collab::where('status', 'accepted')
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                    ->orWhere('recipient_id', $user->id);
            })
            ->with(['sender', 'recipient'])
            ->latest()
            ->get();

        return view('inbox.index', compact('messages', 'sentCollabs', 'connections'));
    }

    public function show(Request $request, Message $message)
    {
        abort_if($request->user()->isAdmin(), 403, 'Admin accounts have no inbox.');

        if ($message->recipient_id !== $request->user()->id) {
            abort(403);
        }

        $message->markAsRead();

        $collab = $message->type === 'collab_invite'
            ? Collab::where('sender_id', $message->sender_id)->where('recipient_id', $request->user()->id)->first()
            : null;

        return view('inbox.show', compact('message', 'collab'));
    }
}
