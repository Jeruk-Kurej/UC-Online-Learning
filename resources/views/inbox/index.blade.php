<x-app-layout>
    {{-- --- UCO Inbox & Network Hub --- --}}
    <div class="inbox-wrapper mx-auto max-w-[1600px] px-4 py-8 sm:px-6 lg:px-8"
         x-data="{ activeTab: 'messages' }">

        {{-- --- Page Header Banner --- --}}
        <x-ui.page-header badge="UCO Network" title="Inbox & Connections" subtitle="Manage your collaboration invitations and connect with partners.">
            <x-slot:actions>
                <a href="{{ route('featured') }}"
                   class="group inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-all duration-200 hover:border-gray-900 hover:bg-gray-900 hover:text-white hover:shadow-md">
                    <i class="bi bi-arrow-left text-base transition-transform duration-200 group-hover:-translate-x-0.5"></i>
                    <span>Back to Featured</span>
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- --- Standard Navigation Tabbar --- --}}
        <div class="mb-8 border-b border-gray-200">
            <nav class="-mb-px flex space-x-6 overflow-x-auto sm:space-x-8" aria-label="Tabs">
                <button @click="activeTab = 'messages'"
                        :class="activeTab === 'messages'
                            ?
                            'border-uco-orange-500 text-uco-orange-600 font-extrabold border-b-2' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-semibold border-b-2'"
                        class="inline-flex items-center gap-2 whitespace-nowrap px-1 py-3 text-sm transition-all">
                    <i class="bi bi-chat-left-text text-base"></i>
                    <span>Inbox & Invitations</span>
                    @if ($messages->total() > 0)
                        <span class="ml-1.5 rounded-full bg-uco-orange-100 px-2 py-0.5 text-xs font-bold text-uco-orange-800">
                            {{ $messages->total() }}
                        </span>
                    @endif
                </button>

                <button @click="activeTab = 'sent'"
                        :class="activeTab === 'sent'
                            ?
                            'border-uco-orange-500 text-uco-orange-600 font-extrabold border-b-2' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-semibold border-b-2'"
                        class="inline-flex items-center gap-2 whitespace-nowrap px-1 py-3 text-sm transition-all">
                    <i class="bi bi-send text-base"></i>
                    <span>Sent Requests</span>
                    @if ($sentCollabs->count() > 0)
                        <span class="ml-1.5 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-bold text-gray-700">
                            {{ $sentCollabs->count() }}
                        </span>
                    @endif
                </button>

                <button @click="activeTab = 'connections'"
                        :class="activeTab === 'connections'
                            ?
                            'border-uco-orange-500 text-uco-orange-600 font-extrabold border-b-2' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-semibold border-b-2'"
                        class="inline-flex items-center gap-2 whitespace-nowrap px-1 py-3 text-sm transition-all">
                    <i class="bi bi-people text-base"></i>
                    <span>My Network</span>
                    @if ($connections->count() > 0)
                        <span class="ml-1.5 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-bold text-gray-700">
                            {{ $connections->count() }}
                        </span>
                    @endif
                </button>
            </nav>
        </div>

        {{-- --- Tabs Content Container --- --}}
        <div class="relative grid min-h-[500px] grid-cols-1">

            {{-- --- Tab 1: Messages / Inbox --- --}}
            <div x-show="activeTab === 'messages'"
                 class="col-span-1 w-full"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2">
                @if ($messages->isEmpty())
                    <div class="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                            <i class="bi bi-chat-left-text text-2xl"></i>
                        </div>
                        <h3 class="mb-1 text-base font-bold text-gray-800">Your Inbox is Empty</h3>
                        <p class="text-sm text-gray-500">When other users request a collaboration, it will show up here.</p>
                    </div>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach ($messages as $message)
                            <a href="{{ route('inbox.show', $message) }}"
                               class="{{ $message->read_at ? 'border-gray-200 border-l-4 border-l-gray-300' : 'border-sky-200 border-l-4 border-l-sky-600 bg-sky-50/20' }} group block rounded-xl border bg-white p-5 shadow-sm transition-all duration-200 hover:shadow-md">
                                <div class="mb-2 flex items-start justify-between">
                                    <h3 class="{{ $message->read_at ? 'text-gray-800' : 'text-sky-700' }} text-base font-extrabold transition-colors group-hover:text-uco-orange-600">
                                        {{ $message->subject }}
                                    </h3>
                                    <span class="ml-2 whitespace-nowrap text-xs font-medium text-gray-400">
                                        {{ $message->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="mb-3 line-clamp-2 text-sm leading-relaxed text-gray-600">
                                    {{ Str::limit(strip_tags($message->body), 150) }}
                                </p>
                                @if ($message->sender)
                                    <div class="flex items-center gap-2 border-t border-gray-100 pt-2">
                                        @if ($message->sender->profile_photo_url)
                                            <img src="{{ $message->sender->profile_photo_url }}" class="h-6 w-6 rounded-full object-cover">
                                        @else
                                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-[10px] font-bold text-gray-700">
                                                {{ substr($message->sender->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <span class="text-xs font-semibold text-gray-700">From: {{ $message->sender->name }}</span>
                                        <span class="rounded bg-gray-100 px-2 py-0.5 text-[10px] font-bold uppercase text-gray-600">
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
            </div>

            {{-- --- Tab 2: Sent Requests --- --}}
            <div x-show="activeTab === 'sent'" x-cloak
                 class="col-span-1 w-full"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2">
                @if ($sentCollabs->isEmpty())
                    <div class="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                            <i class="bi bi-send text-2xl"></i>
                        </div>
                        <h3 class="mb-1 text-base font-bold text-gray-800">No Sent Requests</h3>
                        <p class="text-sm text-gray-500">Explore the business directory and reach out to collaborate with other founders.</p>
                    </div>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach ($sentCollabs as $collab)
                            <div class="flex flex-col justify-between gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-all hover:shadow-md md:flex-row md:items-center">
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('users.show', $collab->recipient) }}" class="flex-shrink-0">
                                        @if ($collab->recipient->profile_photo_url)
                                            <img src="{{ $collab->recipient->profile_photo_url }}" class="h-12 w-12 rounded-lg border border-gray-200 object-cover">
                                        @else
                                            <div class="flex h-12 w-12 items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-lg font-extrabold text-gray-500">
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
                                        <p class="mt-0.5 text-xs font-medium text-gray-500">
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
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-orange-200 bg-orange-50 px-3 py-1.5 text-xs font-bold uppercase text-orange-700">
                                            <i class="bi bi-hourglass-split animate-spin"></i>Pending
                                        </span>
                                    @elseif($collab->status === 'accepted')
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-bold uppercase text-green-700">
                                            <i class="bi bi-check-circle-fill"></i>Connected
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold uppercase text-red-700">
                                            <i class="bi bi-x-circle-fill"></i>Declined
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- --- Tab 3: Connections --- --}}
            <div x-show="activeTab === 'connections'" x-cloak
                 class="col-span-1 w-full"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2">
                @if ($connections->isEmpty())
                    <div class="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                            <i class="bi bi-people text-2xl"></i>
                        </div>
                        <h3 class="mb-1 text-base font-bold text-gray-800">No Connections Yet</h3>
                        <p class="text-sm text-gray-500">Connections are formed when collaboration requests are accepted. Start connecting now!</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($connections as $conn)
                            @include('inbox.partials.connection-card', ['partner' => $conn->partnerFor(auth()->user())])
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
