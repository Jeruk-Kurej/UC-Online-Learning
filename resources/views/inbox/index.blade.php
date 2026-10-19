<x-app-layout>
    @section('title', 'Inbox')

    <div x-data="{ activeTab: 'messages' }">
        <x-ui.page-header title="Inbox" subtitle="Collab requests sent to you, the ones you sent, and the people you are connected with." />

        <div class="tabs mb-6" role="tablist" aria-label="Inbox">
            <x-inbox.tab name="messages" label="Messages" :count="$messages->total()" />
            <x-inbox.tab name="sent" label="Requests I sent" :count="$sentCollabs->count()" />
            <x-inbox.tab name="connections" label="My connections" :count="$connections->count()" />
        </div>

        {{-- --- Messages --- --}}
        <x-inbox.panel name="messages" :cloak="false">
            @if ($messages->isEmpty())
                <x-ui.empty-state title="No messages yet">When someone sends you a collab request, it shows up here.</x-ui.empty-state>
            @else
                <ul class="space-y-3">
                    @foreach ($messages as $message)
                        <li>
                            <a href="{{ route('inbox.show', $message) }}" class="card {{ $message->read_at ? '' : 'border-l-4 border-l-brand-600' }} block transition-colors hover:border-brand-500">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <h2 class="{{ $message->read_at ? 'font-medium' : 'font-semibold' }} text-lg text-brand-900">{{ $message->subject }}</h2>
                                    <p class="text-sm text-gray-600">
                                        @unless ($message->read_at)
                                            <span class="badge badge-info mr-1">New</span>
                                        @endunless
                                        {{ $message->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <p class="mt-1 line-clamp-2 text-base text-gray-700">{{ $message->excerpt }}</p>
                                @if ($message->sender)
                                    <p class="mt-2 text-sm text-gray-600">From {{ $message->sender->name }}</p>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-6">
                    {{ $messages->links() }}
                </div>
            @endif
        </x-inbox.panel>

        {{-- --- Requests I sent --- --}}
        <x-inbox.panel name="sent">
            @if ($sentCollabs->isEmpty())
                <x-ui.empty-state title="You have not sent any requests">Find someone in the directory and send them a collab request.</x-ui.empty-state>
            @else
                <ul class="space-y-3">
                    @foreach ($sentCollabs as $collab)
                        <li class="card flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-4">
                                <x-ui.thumb :src="$collab->recipient->profile_photo_url" :name="$collab->recipient->name" size="h-14 w-14" round class="text-xl" />
                                <div class="min-w-0">
                                    <a href="{{ route('users.show', $collab->recipient) }}" class="block text-lg font-semibold text-brand-900 hover:underline">{{ $collab->recipient->name }}</a>
                                    <p class="text-sm text-gray-600">{{ $collab->recipient->major ?? 'UC student' }}. Sent {{ $collab->created_at->format('j M Y') }}</p>
                                </div>
                            </div>
                            <div>
                                @if ($collab->status === 'pending')
                                    <span class="badge badge-warning text-sm"><i class="bi bi-hourglass-split" aria-hidden="true"></i> Waiting for an answer</span>
                                @elseif ($collab->status === 'accepted')
                                    <span class="badge badge-success text-sm"><i class="bi bi-check-circle" aria-hidden="true"></i> Connected</span>
                                @else
                                    <span class="badge badge-danger text-sm"><i class="bi bi-x-circle" aria-hidden="true"></i> Declined</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-inbox.panel>

        {{-- --- Connections --- --}}
        <x-inbox.panel name="connections">
            @if ($connections->isEmpty())
                <x-ui.empty-state title="No connections yet">You are connected with someone once they accept your collab request.</x-ui.empty-state>
            @else
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($connections as $conn)
                        @include('inbox.partials.connection-card', ['partner' => $conn->partnerFor(auth()->user())])
                    @endforeach
                </div>
            @endif
        </x-inbox.panel>
    </div>
</x-app-layout>
