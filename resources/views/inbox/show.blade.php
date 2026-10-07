<x-app-layout>
    <div class="inbox-detail-wrapper max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Top Back Button Bar --}}
        <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
            <a href="{{ route('inbox.index') }}" 
               class="group inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-900 border border-gray-200 hover:border-gray-900 text-gray-700 hover:text-white rounded-xl font-medium text-sm shadow-sm hover:shadow-md transition-all duration-200">
                <i class="bi bi-arrow-left text-base group-hover:-translate-x-0.5 transition-transform duration-200"></i>
                <span>Back to Inbox</span>
            </a>

            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-200 text-xs font-bold text-blue-700 uppercase tracking-wider">
                <i class="bi bi-envelope-open-fill"></i> Collaboration Message
            </span>
        </div>

        {{-- Main Container Card --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 md:p-8 shadow-sm space-y-6">
            
            {{-- Primary Message Subject (H1 Title) --}}
            <div class="border-b border-gray-100 pb-5">
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight mb-2">
                    {{ $message->subject }}
                </h1>
                <div class="text-xs font-medium text-gray-400 flex items-center gap-2">
                    <i class="bi bi-clock"></i> Received {{ $message->created_at->format('M d, Y \a\t H:i') }} ({{ $message->created_at->diffForHumans() }})
                </div>
            </div>

            {{-- Sender Info Card --}}
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 md:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    @if($message->sender)
                        <a href="{{ route('users.show', $message->sender) }}" class="flex-shrink-0">
                            @if($message->sender->profile_photo_url)
                                <img src="{{ $message->sender->profile_photo_url }}" class="w-12 h-12 rounded-full object-cover border border-gray-200">
                            @else
                                <div class="w-12 h-12 rounded-full bg-sky-100 flex items-center justify-center text-lg font-extrabold text-sky-700 border border-sky-200">
                                    {{ substr($message->sender->name, 0, 1) }}
                                </div>
                            @endif
                        </a>
                        <div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('users.show', $message->sender) }}" class="text-base font-extrabold text-gray-900 hover:text-uco-orange-600 transition-colors">
                                    {{ $message->sender->name }}
                                </a>
                                <span class="px-2 py-0.5 rounded bg-orange-50 text-orange-700 text-[10px] font-bold uppercase border border-orange-200">
                                    {{ $message->sender->current_status }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $message->sender->email }}</div>
                        </div>
                    @else
                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-lg font-extrabold text-gray-600">S</div>
                        <div>
                            <div class="text-base font-extrabold text-gray-900">System Notification</div>
                            <div class="text-xs text-gray-500 mt-0.5">Automated message</div>
                        </div>
                    @endif
                </div>

                @if($message->sender)
                    <a href="{{ route('users.show', $message->sender) }}" 
                       class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-gray-700 text-xs font-semibold shadow-sm transition-all">
                        View Profile
                    </a>
                @endif
            </div>

            {{-- Message Body --}}
            <div class="text-base text-gray-700 leading-relaxed font-normal px-1 py-2 whitespace-pre-line">
                {!! nl2br(e($message->body)) !!}
            </div>

            {{-- Collaboration Invitation Actions Box --}}
            @if($message->type === 'collab_invite')
                @php
                    $collab = \App\Models\Collab::where('sender_id', $message->sender_id)
                        ->where('recipient_id', Auth::id())
                        ->first();
                @endphp

                <div class="rounded-xl border border-sky-200 bg-sky-50 p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-sky-600 text-white flex items-center justify-center text-xl flex-shrink-0">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-sky-900">Collaboration Invitation</h3>
                            <p class="text-xs text-sky-700 mt-0.5 font-medium">
                                {{ $message->sender->name }} wants to connect and build business synergies with you.
                            </p>
                        </div>
                    </div>

                    @if(!$collab)
                        <div class="text-xs text-gray-500 italic pt-2">
                            This collaboration request is no longer active.
                        </div>
                    @elseif($collab->status === 'pending')
                        <div class="flex items-center gap-3 pt-4 border-t border-sky-200/60">
                            <form action="{{ route('collabs.accept', $collab) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-uco-orange-600 hover:bg-uco-orange-700 text-white font-bold text-xs shadow-sm transition-all">
                                    <i class="bi bi-check-circle-fill"></i> Accept Request
                                </button>
                            </form>
                            <form action="{{ route('collabs.reject', $collab) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold text-xs transition-all">
                                    Decline
                                </button>
                            </form>
                        </div>
                    @elseif($collab->status === 'accepted')
                        <div class="pt-4 border-t border-sky-200/60 flex flex-wrap items-center justify-between gap-3">
                            <div class="inline-flex items-center gap-2 text-xs font-extrabold text-green-700">
                                <i class="bi bi-check-circle-fill text-base"></i> Connected with {{ $message->sender->name }}
                            </div>
                            <div class="flex items-center gap-2">
                                @if($message->sender->show_contact_details && $message->sender->whatsapp)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $message->sender->whatsapp) }}" target="_blank" 
                                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white font-bold text-xs transition-all shadow-sm">
                                        <i class="bi bi-whatsapp"></i> Chat WhatsApp
                                    </a>
                                @endif
                                @if($message->sender->businesses->first())
                                    <a href="{{ route('businesses.show', $message->sender->businesses->first()->slug) }}" 
                                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-white border border-sky-300 hover:bg-sky-100 text-sky-700 font-bold text-xs transition-all">
                                        <i class="bi bi-building"></i> View Venture
                                    </a>
                                @endif
                            </div>
                        </div>
                    @elseif($collab->status === 'rejected')
                        <div class="pt-2 text-xs font-bold text-red-600 flex items-center gap-1.5">
                            <i class="bi bi-x-circle-fill"></i> You declined this request.
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
