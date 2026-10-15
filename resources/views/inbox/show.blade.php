<x-app-layout>
    <div class="inbox-detail-wrapper mx-auto max-w-[1200px] px-4 py-8 sm:px-6 lg:px-8">

        {{-- --- Top Back Button Bar --- --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <a href="{{ route('inbox.index') }}"
               class="group inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-all duration-200 hover:border-gray-900 hover:bg-gray-900 hover:text-white hover:shadow-md">
                <i class="bi bi-arrow-left text-base transition-transform duration-200 group-hover:-translate-x-0.5"></i>
                <span>Back to Inbox</span>
            </a>

            <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-blue-700">
                <i class="bi bi-envelope-open-fill"></i> Collaboration Message
            </span>
        </div>

        {{-- --- Main Container Card --- --}}
        <div class="space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm md:p-8">

            {{-- --- Primary Message Subject (H1 Title) --- --}}
            <div class="border-b border-gray-100 pb-5">
                <h1 class="mb-2 text-2xl font-extrabold tracking-tight text-gray-900 md:text-3xl">
                    {{ $message->subject }}
                </h1>
                <div class="flex items-center gap-2 text-xs font-medium text-gray-400">
                    <i class="bi bi-clock"></i> Received {{ $message->created_at->format('M d, Y \a\t H:i') }} ({{ $message->created_at->diffForHumans() }})
                </div>
            </div>

            {{-- --- Sender Info Card --- --}}
            <div class="flex flex-col justify-between gap-4 rounded-xl border border-gray-200 bg-gray-50 p-4 md:flex-row md:items-center md:p-5">
                <div class="flex items-center gap-3.5">
                    @if ($message->sender)
                        <a href="{{ route('users.show', $message->sender) }}" class="flex-shrink-0">
                            @if ($message->sender->profile_photo_url)
                                <img src="{{ $message->sender->profile_photo_url }}" class="h-12 w-12 rounded-full border border-gray-200 object-cover">
                            @else
                                <div class="flex h-12 w-12 items-center justify-center rounded-full border border-sky-200 bg-sky-100 text-lg font-extrabold text-sky-700">
                                    {{ substr($message->sender->name, 0, 1) }}
                                </div>
                            @endif
                        </a>
                        <div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('users.show', $message->sender) }}" class="text-base font-extrabold text-gray-900 transition-colors hover:text-uco-orange-600">
                                    {{ $message->sender->name }}
                                </a>
                                <span class="rounded border border-orange-200 bg-orange-50 px-2 py-0.5 text-[10px] font-bold uppercase text-orange-700">
                                    {{ $message->sender->current_status }}
                                </span>
                            </div>
                            <div class="mt-0.5 text-xs text-gray-500">{{ $message->sender->email }}</div>
                        </div>
                    @else
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-200 text-lg font-extrabold text-gray-600">S</div>
                        <div>
                            <div class="text-base font-extrabold text-gray-900">System Notification</div>
                            <div class="mt-0.5 text-xs text-gray-500">Automated message</div>
                        </div>
                    @endif
                </div>

                @if ($message->sender)
                    <a href="{{ route('users.show', $message->sender) }}"
                       class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-700 shadow-sm transition-all hover:bg-gray-100">
                        View Profile
                    </a>
                @endif
            </div>

            {{-- --- Message Body --- --}}
            <div class="whitespace-pre-line px-1 py-2 text-base font-normal leading-relaxed text-gray-700">
                {!! nl2br(e($message->body)) !!}
            </div>

            {{-- --- Collaboration Invitation Actions Box --- --}}
            @if ($message->type === 'collab_invite')
                <div class="space-y-4 rounded-xl border border-sky-200 bg-sky-50 p-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-sky-600 text-xl text-white">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-sky-900">Collaboration Invitation</h3>
                            <p class="mt-0.5 text-xs font-medium text-sky-700">
                                {{ $message->sender->name }} wants to connect and build business synergies with you.
                            </p>
                        </div>
                    </div>

                    @if (!$collab)
                        <div class="pt-2 text-xs italic text-gray-500">
                            This collaboration request is no longer active.
                        </div>
                    @elseif($collab->status === 'pending')
                        <div class="flex items-center gap-3 border-t border-sky-200/60 pt-4">
                            <form action="{{ route('collabs.accept', $collab) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-lg bg-uco-orange-600 px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:bg-uco-orange-700">
                                    <i class="bi bi-check-circle-fill"></i> Accept Request
                                </button>
                            </form>
                            <form action="{{ route('collabs.reject', $collab) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-xs font-semibold text-gray-700 transition-all hover:bg-gray-50">
                                    Decline
                                </button>
                            </form>
                        </div>
                    @elseif($collab->status === 'accepted')
                        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-sky-200/60 pt-4">
                            <div class="inline-flex items-center gap-2 text-xs font-extrabold text-green-700">
                                <i class="bi bi-check-circle-fill text-base"></i> Connected with {{ $message->sender->name }}
                            </div>
                            <div class="flex items-center gap-2">
                                @if ($message->sender->show_contact_details && $message->sender->whatsapp)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $message->sender->whatsapp) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition-all hover:bg-green-700">
                                        <i class="bi bi-whatsapp"></i> Chat WhatsApp
                                    </a>
                                @endif
                                @if ($message->sender->businesses->first())
                                    <a href="{{ route('businesses.show', $message->sender->businesses->first()->slug) }}"
                                       class="inline-flex items-center gap-1.5 rounded-lg border border-sky-300 bg-white px-3.5 py-2 text-xs font-bold text-sky-700 transition-all hover:bg-sky-100">
                                        <i class="bi bi-building"></i> View Venture
                                    </a>
                                @endif
                            </div>
                        </div>
                    @elseif($collab->status === 'rejected')
                        <div class="flex items-center gap-1.5 pt-2 text-xs font-bold text-red-600">
                            <i class="bi bi-x-circle-fill"></i> You declined this request.
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
