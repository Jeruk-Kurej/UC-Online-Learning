<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('inbox.index') }}" class="text-gray-500 hover:text-gray-700 transition">
                <i class="bi bi-arrow-left"></i> Back to Inbox
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $message->subject }}
            </h2>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    @if($message->sender)
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
                            {{ substr($message->sender->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-medium">{{ $message->sender->name }}</div>
                            <div class="text-xs text-gray-500">{{ $message->sender->email }}</div>
                        </div>
                    @else
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-700 font-bold">
                            S
                        </div>
                        <div>
                            <div class="font-medium">System</div>
                        </div>
                    @endif
                </div>
                <div class="text-sm text-gray-500">
                    {{ $message->created_at->format('M d, Y H:i') }}
                </div>
            </div>

            <div class="prose max-w-none text-gray-700 whitespace-pre-wrap">
                {!! nl2br(e($message->body)) !!}
            </div>

            @if($message->type === 'collab_invite')
                @php
                    $collab = \App\Models\Collab::where('sender_id', $message->sender_id)
                        ->where('recipient_id', Auth::id())
                        ->first();
                @endphp
                <div class="mt-8 p-4 bg-blue-50 border border-blue-100 rounded-lg flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <i class="bi bi-person-lines-fill text-blue-500 text-xl"></i>
                        <div>
                            <h4 class="font-semibold text-blue-900 text-sm">Collaboration Invitation</h4>
                            <p class="text-sm text-blue-800 mt-1">This user wants to connect and collaborate with you.</p>
                        </div>
                    </div>
                    @if(!$collab)
                        <div class="mt-2 text-sm text-gray-500 italic">
                            This collaboration request is no longer valid.
                        </div>
                    @elseif($collab->status === 'pending')
                        <div class="flex gap-2 mt-2">
                            <form action="{{ route('collabs.accept', $collab) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-uco btn-uco-primary px-4 py-2 text-sm font-bold rounded-lg shadow-sm">Accept Request</button>
                            </form>
                            <form action="{{ route('collabs.reject', $collab) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-uco bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 text-sm font-bold rounded-lg shadow-sm">Reject</button>
                            </form>
                        </div>
                    @elseif($collab->status === 'accepted')
                        <div class="mt-2 text-sm font-bold text-green-700 flex items-center gap-2">
                            <i class="bi bi-check-circle-fill"></i> You are connected with this user.
                        </div>
                    @elseif($collab->status === 'rejected')
                        <div class="mt-2 text-sm font-bold text-red-700 flex items-center gap-2">
                            <i class="bi bi-x-circle-fill"></i> You have rejected this request.
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
