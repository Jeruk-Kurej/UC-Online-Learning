<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class InboxController extends Controller
{
    public function index(Request $request)
    {
        $messages = $request->user()->receivedMessages()->latest()->paginate(15);
        return view('inbox.index', compact('messages'));
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
