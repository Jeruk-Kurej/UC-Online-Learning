<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

use App\Models\Collab;

class InboxController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $messages = $user->receivedMessages()->latest()->paginate(15);
        
        $sentCollabs = $user->sentCollabs()->with(['recipient'])->latest()->get();
        
        $connections = Collab::where('status', 'accepted')
            ->where(function($q) use ($user) {
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
        if ($message->recipient_id !== $request->user()->id) {
            abort(403);
        }

        $message->markAsRead();

        return view('inbox.show', compact('message'));
    }
}
