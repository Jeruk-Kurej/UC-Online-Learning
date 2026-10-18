<x-app-layout>
    {{-- --- UCO Inbox & Network Hub --- --}}
    <div class="inbox-wrapper mx-auto max-w-[1600px] px-4 py-8 sm:px-6 lg:px-8"
         x-data="{ activeTab: 'messages' }">

        {{-- --- Page Header Banner --- --}}
        <x-ui.page-header badge="UCO Network" title="Inbox & Connections" subtitle="Manage your collaboration invitations and connect with partners.">
            <x-slot:actions>
                <x-ui.back-link :href="route('featured')" label="Back to Featured" />
            </x-slot:actions>
        </x-ui.page-header>

        {{-- --- Standard Navigation Tabbar --- --}}
        <div class="mb-8 border-b border-gray-200">
            <nav class="-mb-px flex space-x-6 overflow-x-auto sm:space-x-8" aria-label="Tabs">
                <x-inbox.tab name="messages" icon="bi-chat-left-text" label="Inbox & Invitations" :count="$messages->total()" highlight />
                <x-inbox.tab name="sent" icon="bi-send" label="Sent Requests" :count="$sentCollabs->count()" />
                <x-inbox.tab name="connections" icon="bi-people" label="My Network" :count="$connections->count()" />
            </nav>
        </div>

        {{-- --- Tabs Content Container --- --}}
        <div class="relative grid min-h-[500px] grid-cols-1">

            {{-- --- Tab 1: Messages / Inbox --- --}}
            <x-inbox.panel name="messages" :cloak="false">
                @if ($messages->isEmpty())
                    <x-inbox.empty-state icon="bi-chat-left-text" title="Your Inbox is Empty">When other users request a collaboration, it will show up here.</x-inbox.empty-state>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach ($messages as $message)
                            <a href="{{ route('inbox.show', $message) }}"
                               class="{{ $message->read_at ? 'border-gray-200 border-l-4 border-l-gray-300' : 'border-sky-200 border-l-4 border-l-sky-600 bg-sky-50/20' }} group block rounded-xl border bg-white p-5 shadow-sm transition-all duration-200 hover:shadow-md">
                                <div class="mb-2 flex items-start justify-between">
                                    <h3 class="{{ $message->read_at ? 'text-gray-800' : 'text-sky-700' }} text-base font-bold transition-colors group-hover:text-uco-orange-600">
                                        {{ $message->subject }}
                                    </h3>
                                    <span class="ml-2 whitespace-nowrap text-xs font-medium text-gray-600">
                                        {{ $message->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="mb-3 line-clamp-2 text-sm leading-relaxed text-gray-600">
                                    {{ $message->excerpt }}
                                </p>
                                @if ($message->sender)
                                    <div class="flex items-center gap-2 border-t border-gray-100 pt-2">
                                        @if ($message->sender->profile_photo_url)
                                            <img src="{{ $message->sender->profile_photo_url }}" class="h-6 w-6 rounded-full object-cover">
                                        @else
                                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-xs font-bold text-gray-700">
                                                {{ substr($message->sender->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <span class="text-xs font-semibold text-gray-700">From: {{ $message->sender->name }}</span>
                                        <span class="rounded bg-gray-100 px-2 py-0.5 text-xs font-bold text-gray-600">
                                            {{ $message->sender->current_status }}
                                        </span>
                                    </div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        {{ $messages->links() }}
                    </div>
                @endif
            </x-inbox.panel>

            {{-- --- Tab 2: Sent Requests --- --}}
            <x-inbox.panel name="sent">
                @if ($sentCollabs->isEmpty())
                    <x-inbox.empty-state icon="bi-send" title="No Sent Requests">Explore the business directory and reach out to collaborate with other founders.</x-inbox.empty-state>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach ($sentCollabs as $collab)
                            <div class="flex flex-col justify-between gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-all hover:shadow-md md:flex-row md:items-center">
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('users.show', $collab->recipient) }}" class="flex-shrink-0">
                                        @if ($collab->recipient->profile_photo_url)
                                            <img src="{{ $collab->recipient->profile_photo_url }}" class="h-12 w-12 rounded-lg border border-gray-200 object-cover">
                                        @else
                                            <div class="flex h-12 w-12 items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-lg font-bold text-gray-600">
                                                {{ substr($collab->recipient->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </a>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900 transition-colors hover:text-uco-orange-600">
                                            <a href="{{ route('users.show', $collab->recipient) }}">
                                                {{ $collab->recipient->name }}
                                            </a>
                                        </h3>
                                        <p class="mt-0.5 text-xs font-medium text-gray-600">
                                            {{ $collab->recipient->major ?? 'UC Student' }} • Sent {{ $collab->created_at->format('M d, Y') }}
                                        </p>
                                        @if ($collab->recipient->businesses->first())
                                            <a href="{{ route('businesses.show', $collab->recipient->businesses->first()->slug) }}" class="mt-1 inline-flex items-center gap-1 text-xs font-semibold text-uco-orange-600 hover:underline">
                                                <i class="bi bi-building"></i>{{ $collab->recipient->businesses->first()->name }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('users.show', $collab->recipient) }}"
                                       class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-gray-50 px-3.5 py-2 text-xs font-semibold text-gray-700 transition-all hover:bg-gray-100">
                                        View Profile
                                    </a>
                                    @if ($collab->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-orange-200 bg-orange-50 px-3 py-1.5 text-xs font-bold text-orange-700">
                                            <i class="bi bi-hourglass-split animate-spin"></i>Pending
                                        </span>
                                    @elseif($collab->status === 'accepted')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-bold text-green-700">
                                            <i class="bi bi-check-circle-fill"></i>Connected
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700">
                                            <i class="bi bi-x-circle-fill"></i>Declined
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-inbox.panel>

            {{-- --- Tab 3: Connections --- --}}
            <x-inbox.panel name="connections">
                @if ($connections->isEmpty())
                    <x-inbox.empty-state icon="bi-people" title="No Connections Yet">Connections are formed when collaboration requests are accepted. Start connecting now!</x-inbox.empty-state>
                @else
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($connections as $conn)
                            @include('inbox.partials.connection-card', ['partner' => $conn->partnerFor(auth()->user())])
                        @endforeach
                    </div>
                @endif
            </x-inbox.panel>
        </div>
    </div>
</x-app-layout>
