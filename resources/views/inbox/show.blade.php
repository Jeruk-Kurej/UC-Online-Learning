<x-app-layout>
    @section('title', $message->subject)

    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <x-ui.back-link :href="route('inbox.index')" label="Back to inbox" />
        </div>

        <article class="card space-y-6">
            <header class="border-b border-line pb-5">
                <h1 class="page-title !text-2xl sm:!text-3xl">{{ $message->subject }}</h1>
                <p class="mt-2 text-sm text-gray-600">Received {{ $message->created_at->format('j M Y, H:i') }} ({{ $message->created_at->diffForHumans() }})</p>
            </header>

            {{-- --- Sender --- --}}
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                @if ($message->sender)
                    <div class="flex items-center gap-4">
                        <x-ui.thumb :src="$message->sender->profile_photo_url" :name="$message->sender->name" size="h-14 w-14" round class="text-xl" />
                        <div class="min-w-0">
                            <a href="{{ route('users.show', $message->sender) }}" class="block text-lg font-semibold text-brand-900 hover:underline">{{ $message->sender->name }}</a>
                            <p class="text-sm text-gray-600">{{ $message->sender->current_status }}. {{ $message->sender->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('users.show', $message->sender) }}" class="btn btn-secondary btn-sm">View profile</a>
                @else
                    <p class="text-lg font-semibold text-gray-900">System message</p>
                @endif
            </div>

            <div class="whitespace-pre-line text-lg text-gray-800">{!! nl2br(e($message->body)) !!}</div>

            {{-- --- Collab request --- --}}
            @if ($message->type === 'collab_invite')
                <section class="rounded-md border border-brand-200 bg-brand-50 p-5">
                    <h2 class="text-lg font-semibold text-brand-900">Collab request</h2>
                    <p class="mt-1 text-base text-gray-800">{{ $message->sender->name }} would like to connect with you.</p>

                    @if (!$collab)
                        <p class="mt-4 text-base text-gray-700">This request is no longer active.</p>
                    @elseif ($collab->status === 'pending')
                        <div class="mt-4 flex flex-wrap gap-3">
                            <form action="{{ route('collabs.accept', $collab) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">Accept request</button>
                            </form>
                            <form action="{{ route('collabs.reject', $collab) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-secondary">Decline</button>
                            </form>
                        </div>
                    @elseif ($collab->status === 'accepted')
                        <div class="mt-4 space-y-3">
                            <p class="text-base font-semibold text-green-800"><i class="bi bi-check-circle" aria-hidden="true"></i> You are connected with {{ $message->sender->name }}.</p>
                            <div class="flex flex-wrap gap-2">
                                @if ($message->sender->show_contact_details && $message->sender->whatsapp)
                                    <a href="{{ $message->sender->whatsapp_link }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm"><i class="bi bi-whatsapp" aria-hidden="true"></i> Message on WhatsApp</a>
                                @endif
                                @if ($message->sender->businesses->first())
                                    <a href="{{ route('businesses.show', $message->sender->businesses->first()->slug) }}" class="btn btn-secondary btn-sm">View their business</a>
                                @endif
                            </div>
                        </div>
                    @elseif ($collab->status === 'rejected')
                        <p class="mt-4 text-base font-semibold text-red-800">You declined this request.</p>
                    @endif
                </section>
            @endif
        </article>
    </div>
</x-app-layout>
