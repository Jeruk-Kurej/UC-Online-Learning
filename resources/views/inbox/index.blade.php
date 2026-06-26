<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inbox') }}
        </h2>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
        <div class="p-6 text-gray-900">
            @if($messages->isEmpty())
                <p class="text-gray-500 text-center py-8">Your inbox is empty.</p>
            @else
                <div class="flex flex-col gap-4">
                    @foreach($messages as $message)
                        <a href="{{ route('inbox.show', $message) }}" class="block p-4 rounded-lg border {{ $message->read_at ? 'bg-gray-50 border-gray-200' : 'bg-white border-blue-200 shadow-sm' }} hover:shadow-md transition">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-bold {{ $message->read_at ? 'text-gray-700' : 'text-blue-700' }}">
                                    {{ $message->subject }}
                                </h3>
                                <span class="text-xs text-gray-500">{{ $message->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit(strip_tags($message->body), 100) }}</p>
                            @if($message->sender)
                                <div class="mt-2 text-xs text-gray-500">
                                    From: <span class="font-medium text-gray-700">{{ $message->sender->name }}</span>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
                
                <div class="mt-4">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
