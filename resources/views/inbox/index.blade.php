<x-app-layout>
    {{-- UCO Inbox & Network Hub --}}
    <div class="inbox-wrapper max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8" 
         x-data="{ activeTab: 'messages' }">
        
        {{-- Page Header Banner --}}
        <section class="relative overflow-hidden rounded-xl border border-gray-200 bg-white px-6 py-6 shadow-sm md:px-8 mb-8 reveal-on-scroll">
            <div class="relative z-10 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-1">
                    <span class="inline-flex items-center rounded-md border border-uco-orange-200 bg-uco-orange-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-uco-orange-700">
                        UCO Network
                    </span>
                    <h1 class="text-3xl font-extrabold text-soft-gray-900 md:text-4xl">Inbox & Connections</h1>
                    <p class="text-sm text-soft-gray-600 mt-1">Manage your collaboration invitations and connect with partners.</p>
                </div>

                <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full lg:w-auto">
                    <a href="{{ route('featured') }}" 
                       class="group inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-900 border border-gray-200 hover:border-gray-900 text-gray-700 hover:text-white rounded-xl font-medium text-sm shadow-sm hover:shadow-md transition-all duration-200">
                        <i class="bi bi-arrow-left text-base group-hover:-translate-x-0.5 transition-transform duration-200"></i>
                        <span>Back to Featured</span>
                    </a>
                </div>
            </div>
        </section>

        {{-- Standard Navigation Tabbar --}}
        <div class="border-b border-gray-200 mb-8">
            <nav class="-mb-px flex space-x-6 sm:space-x-8 overflow-x-auto" aria-label="Tabs">
                <button @click="activeTab = 'messages'" 
                        :class="activeTab === 'messages' 
                            ? 'border-uco-orange-500 text-uco-orange-600 font-extrabold border-b-2' 
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-semibold border-b-2'"
                        class="inline-flex items-center gap-2 py-3 px-1 text-sm whitespace-nowrap transition-all">
                    <i class="bi bi-chat-left-text text-base"></i>
                    <span>Inbox & Invitations</span>
                    @if($messages->total() > 0)
                        <span class="ml-1.5 px-2 py-0.5 text-xs font-bold rounded-full bg-uco-orange-100 text-uco-orange-800">
                            {{ $messages->total() }}
                        </span>
                    @endif
                </button>

                <button @click="activeTab = 'sent'" 
                        :class="activeTab === 'sent' 
                            ? 'border-uco-orange-500 text-uco-orange-600 font-extrabold border-b-2' 
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-semibold border-b-2'"
                        class="inline-flex items-center gap-2 py-3 px-1 text-sm whitespace-nowrap transition-all">
                    <i class="bi bi-send text-base"></i>
                    <span>Sent Requests</span>
                    @if($sentCollabs->count() > 0)
                        <span class="ml-1.5 px-2 py-0.5 text-xs font-bold rounded-full bg-gray-100 text-gray-700">
                            {{ $sentCollabs->count() }}
                        </span>
                    @endif
                </button>

                <button @click="activeTab = 'connections'" 
                        :class="activeTab === 'connections' 
                            ? 'border-uco-orange-500 text-uco-orange-600 font-extrabold border-b-2' 
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-semibold border-b-2'"
                        class="inline-flex items-center gap-2 py-3 px-1 text-sm whitespace-nowrap transition-all">
                    <i class="bi bi-people text-base"></i>
                    <span>My Network</span>
                    @if($connections->count() > 0)
                        <span class="ml-1.5 px-2 py-0.5 text-xs font-bold rounded-full bg-gray-100 text-gray-700">
                            {{ $connections->count() }}
                        </span>
                    @endif
                </button>
            </nav>
        </div>

        {{-- Tabs Content Container --}}
        <div class="grid grid-cols-1 min-h-[500px] relative">
            
            {{-- Tab 1: Messages / Inbox --}}
            <div x-show="activeTab === 'messages'"
                 class="col-span-1 w-full"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2">
                @if($messages->isEmpty())
                    <div class="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                        <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                            <i class="bi bi-chat-left-text text-2xl"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-800 mb-1">Your Inbox is Empty</h3>
                        <p class="text-sm text-gray-500">When other users request a collaboration, it will show up here.</p>
                    </div>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach($messages as $message)
                            <a href="{{ route('inbox.show', $message) }}" 
                               class="group block rounded-xl border bg-white p-5 shadow-sm hover:shadow-md transition-all duration-200 {{ $message->read_at ? 'border-gray-200 border-l-4 border-l-gray-300' : 'border-sky-200 border-l-4 border-l-sky-600 bg-sky-50/20' }}">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-base font-extrabold {{ $message->read_at ? 'text-gray-800' : 'text-sky-700' }} group-hover:text-uco-orange-600 transition-colors">
                                        {{ $message->subject }}
                                    </h3>
                                    <span class="text-xs font-medium text-gray-400 whitespace-nowrap ml-2">
                                        {{ $message->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2 leading-relaxed">
                                    {{ Str::limit(strip_tags($message->body), 150) }}
                                </p>
                                @if($message->sender)
                                    <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                                        @if($message->sender->profile_photo_url)
                                            <img src="{{ $message->sender->profile_photo_url }}" class="w-6 h-6 rounded-full object-cover">
                                        @else
                                            <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-700">
                                                {{ substr($message->sender->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <span class="text-xs font-semibold text-gray-700">From: {{ $message->sender->name }}</span>
                                        <span class="px-2 py-0.5 rounded bg-gray-100 text-[10px] font-bold text-gray-600 uppercase">
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

            {{-- Tab 2: Sent Requests --}}
            <div x-show="activeTab === 'sent'" x-cloak
                 class="col-span-1 w-full"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2">
                @if($sentCollabs->isEmpty())
                    <div class="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                        <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                            <i class="bi bi-send text-2xl"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-800 mb-1">No Sent Requests</h3>
                        <p class="text-sm text-gray-500">Explore the business directory and reach out to collaborate with other founders.</p>
                    </div>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach($sentCollabs as $collab)
                            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition-all flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('users.show', $collab->recipient) }}" class="flex-shrink-0">
                                        @if($collab->recipient->profile_photo_url)
                                            <img src="{{ $collab->recipient->profile_photo_url }}" class="w-12 h-12 rounded-lg object-cover border border-gray-200">
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-lg font-extrabold text-gray-500 border border-gray-200">
                                                {{ substr($collab->recipient->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </a>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900 hover:text-uco-orange-600 transition-colors">
                                            <a href="{{ route('users.show', $collab->recipient) }}">
                                                {{ $collab->recipient->name }}
                                            </a>
                                        </h3>
                                        <p class="text-xs text-gray-500 mt-0.5 font-medium">
                                            {{ $collab->recipient->major ?? 'UC Student' }} • Sent {{ $collab->created_at->format('M d, Y') }}
                                        </p>
                                        @if($collab->recipient->businesses->first())
                                            <a href="{{ route('businesses.show', $collab->recipient->businesses->first()->slug) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-uco-orange-600 hover:underline mt-1">
                                                <i class="bi bi-building"></i>{{ $collab->recipient->businesses->first()->name }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('users.show', $collab->recipient) }}" 
                                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-semibold transition-all">
                                        View Profile
                                    </a>
                                    @if($collab->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-orange-50 border border-orange-200 text-xs font-bold text-orange-700 uppercase">
                                            <i class="bi bi-hourglass-split animate-spin"></i>Pending
                                        </span>
                                    @elseif($collab->status === 'accepted')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-green-50 border border-green-200 text-xs font-bold text-green-700 uppercase">
                                            <i class="bi bi-check-circle-fill"></i>Connected
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-red-50 border border-red-200 text-xs font-bold text-red-700 uppercase">
                                            <i class="bi bi-x-circle-fill"></i>Declined
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Tab 3: Connections --}}
            <div x-show="activeTab === 'connections'" x-cloak
                 class="col-span-1 w-full"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2">
                @if($connections->isEmpty())
                    <div class="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                        <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                            <i class="bi bi-people text-2xl"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-800 mb-1">No Connections Yet</h3>
                        <p class="text-sm text-gray-500">Connections are formed when collaboration requests are accepted. Start connecting now!</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($connections as $conn)
                            @php
                                $partner = ($conn->sender_id === auth()->id()) ? $conn->recipient : $conn->sender;
                            @endphp
                            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start gap-4 mb-4">
                                        <a href="{{ route('users.show', $partner) }}" class="flex-shrink-0">
                                            @if($partner->profile_photo_url)
                                                <img src="{{ $partner->profile_photo_url }}" class="w-14 h-14 rounded-lg object-cover border border-gray-200">
                                            @else
                                                <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center text-xl font-extrabold text-gray-500 border border-gray-200">
                                                    {{ substr($partner->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </a>
                                        <div>
                                            <h3 class="text-base font-bold text-gray-900 hover:text-uco-orange-600 transition-colors">
                                                <a href="{{ route('users.show', $partner) }}">
                                                    {{ $partner->name }}
                                                </a>
                                            </h3>
                                            <p class="text-xs text-gray-500 font-medium mt-0.5">{{ $partner->major }}</p>
                                            <span class="inline-block mt-1.5 px-2 py-0.5 bg-orange-50 text-orange-700 rounded text-[10px] font-bold uppercase">
                                                {{ $partner->current_status }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    @if($partner->businesses->first())
                                        <div class="bg-gray-50 rounded-lg p-3 mb-4 border border-gray-100">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Venture / Business</span>
                                            <span class="text-sm font-bold text-gray-800 block mt-0.5">{{ $partner->businesses->first()->name }}</span>
                                            <span class="text-xs text-gray-500 block mt-0.5">{{ $partner->businesses->first()->city }}, {{ $partner->businesses->first()->province }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 pt-4 border-t border-gray-100 mt-2">
                                    @if($partner->show_contact_details)
                                        @if($partner->whatsapp)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $partner->whatsapp) }}" target="_blank" 
                                               class="flex-1 inline-flex items-center justify-center gap-1.5 h-9 rounded-lg border border-green-300 bg-green-50 hover:bg-green-100 text-green-700 font-bold text-xs transition-all">
                                                <i class="bi bi-whatsapp"></i> Chat WA
                                            </a>
                                        @endif
                                        @if($partner->personal_email)
                                            <a href="mailto:{{ $partner->personal_email }}" 
                                               class="flex-1 inline-flex items-center justify-center gap-1.5 h-9 rounded-lg border border-blue-300 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs transition-all">
                                                <i class="bi bi-envelope"></i> Email
                                            </a>
                                        @endif
                                    @else
                                        <span class="flex-1 inline-flex items-center justify-center gap-1.5 h-9 rounded-lg bg-gray-100 text-gray-500 text-xs font-semibold border border-dashed border-gray-300">
                                            <i class="bi bi-shield-lock-fill"></i> Details Hidden
                                        </span>
                                    @endif
                                    
                                    @if($partner->businesses->first())
                                        <a href="{{ route('businesses.show', $partner->businesses->first()->slug) }}" 
                                           class="w-9 h-9 rounded-lg border border-gray-200 hover:border-gray-900 bg-white hover:bg-gray-900 text-gray-700 hover:text-white flex items-center justify-center transition-all"
                                           title="View Business Profile">
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
