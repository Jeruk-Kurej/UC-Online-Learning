<x-app-layout>
    {{-- Sub-header: Navigation bar --}}
    <x-slot name="header">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <a href="{{ route('inbox.index') }}" 
               style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: #475569; text-decoration: none; transition: 0.2s;"
               onmouseover="this.style.color='#0f172a'" onmouseout="this.style.color='#475569'">
                <i class="bi bi-arrow-left"></i> Back to Inbox
            </a>
            <span style="font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 20px; background: #eff6ff; color: #1d4ed8; text-transform: uppercase; letter-spacing: 0.5px;">
                Collaboration Message
            </span>
        </div>
    </x-slot>

    {{-- Main Container Card --}}
    <div style="max-width: 900px; margin: 35px auto; padding: 0 20px; font-family: 'Inter', -apple-system, sans-serif;">
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 35px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);">
            
            {{-- Primary Message Subject (H1 Title) --}}
            <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 24px;">
                <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; letter-spacing: -0.8px; margin: 0 0 8px 0;">
                    {{ $message->subject }}
                </h1>
                <div style="font-size: 12px; font-weight: 600; color: #94a3b8; display: flex; align-items: center; gap: 6px;">
                    <i class="bi bi-clock"></i> Received {{ $message->created_at->format('M d, Y \a\t H:i') }} ({{ $message->created_at->diffForHumans() }})
                </div>
            </div>

            {{-- Sender Info Card --}}
            <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px; padding: 18px 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    @if($message->sender)
                        <a href="{{ route('users.show', $message->sender) }}" style="text-decoration: none;">
                            @if($message->sender->profile_photo_url)
                                <img src="{{ $message->sender->profile_photo_url }}" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 1px solid #e2e8f0;">
                            @else
                                <div style="width: 44px; height: 44px; background: #e0f2fe; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 800; color: #0369a1;">
                                    {{ substr($message->sender->name, 0, 1) }}
                                </div>
                            @endif
                        </a>
                        <div>
                            <div style="font-size: 15px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                                <a href="{{ route('users.show', $message->sender) }}" style="color: inherit; text-decoration: none;" onmouseover="this.style.color='#f97316'" onmouseout="this.style.color='#0f172a'">
                                    {{ $message->sender->name }}
                                </a>
                                <span style="font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 4px; background: #fff7ed; color: #c2410c; text-transform: uppercase;">
                                    {{ $message->sender->current_status }}
                                </span>
                            </div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">{{ $message->sender->email }}</div>
                        </div>
                    @else
                        <div style="width: 44px; height: 44px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 800; color: #64748b;">S</div>
                        <div>
                            <div style="font-size: 15px; font-weight: 800; color: #0f172a;">System Notification</div>
                            <div style="font-size: 12px; color: #64748b;">Automated message</div>
                        </div>
                    @endif
                </div>

                @if($message->sender)
                    <a href="{{ route('users.show', $message->sender) }}" 
                       style="padding: 8px 14px; border-radius: 8px; border: 1px solid #e2e8f0; background: white; font-size: 12px; font-weight: 700; color: #475569; text-decoration: none; transition: 0.2s;"
                       onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">
                        View Profile
                    </a>
                @endif
            </div>

            {{-- Message Body --}}
            <div style="font-size: 15px; color: #334155; line-height: 1.7; margin-bottom: 30px; font-weight: 400; padding: 0 4px;">
                {!! nl2br(e($message->body)) !!}
            </div>

            {{-- Collaboration Invitation Actions Box --}}
            @if($message->type === 'collab_invite')
                @php
                    $collab = \App\Models\Collab::where('sender_id', $message->sender_id)
                        ->where('recipient_id', Auth::id())
                        ->first();
                @endphp

                <div style="border-radius: 14px; border: 1px solid #bae6fd; background: #f0f9ff; padding: 24px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #0284c7; color: white; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 15px; font-weight: 800; color: #0369a1; margin: 0;">Collaboration Invitation</h3>
                            <p style="font-size: 13px; color: #0284c7; margin: 2px 0 0 0; font-weight: 500;">
                                {{ $message->sender->name }} wants to connect and build business synergies with you.
                            </p>
                        </div>
                    </div>

                    @if(!$collab)
                        <div style="font-size: 13px; color: #64748b; font-style: italic; margin-top: 10px;">
                            This collaboration request is no longer active.
                        </div>
                    @elseif($collab->status === 'pending')
                        <div style="display: flex; gap: 12px; margin-top: 16px; border-top: 1px solid #e0f2fe; padding-top: 16px;">
                            <form action="{{ route('collabs.accept', $collab) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        style="padding: 10px 22px; border-radius: 10px; background: #f97316; border: none; color: white; font-size: 13px; font-weight: 800; cursor: pointer; transition: 0.2s;"
                                        onmouseover="this.style.background='#ea580c'" onmouseout="this.style.background='#f97316'">
                                    <i class="bi bi-check-circle-fill" style="margin-right: 6px;"></i> Accept Request
                                </button>
                            </form>
                            <form action="{{ route('collabs.reject', $collab) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        style="padding: 10px 22px; border-radius: 10px; background: white; border: 1px solid #cbd5e1; color: #475569; font-size: 13px; font-weight: 700; cursor: pointer; transition: 0.2s;"
                                        onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                                    Decline
                                </button>
                            </form>
                        </div>
                    @elseif($collab->status === 'accepted')
                        <div style="margin-top: 14px; border-top: 1px solid #e0f2fe; padding-top: 14px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 800; color: #15803d;">
                                <i class="bi bi-check-circle-fill" style="font-size: 16px;"></i> You are connected with {{ $message->sender->name }}.
                            </div>
                            <div style="display: flex; gap: 8px;">
                                @if($message->sender->whatsapp)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $message->sender->whatsapp) }}" target="_blank" 
                                       style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; background: #22c55e; color: white; font-size: 12px; font-weight: 800; text-decoration: none;">
                                        <i class="bi bi-whatsapp"></i> Chat WhatsApp
                                    </a>
                                @endif
                                <a href="{{ route('users.show', $message->sender) }}" 
                                   style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; background: white; border: 1px solid #bae6fd; color: #0284c7; font-size: 12px; font-weight: 800; text-decoration: none;">
                                    <i class="bi bi-person-fill"></i> View Partner Profile
                                </a>
                            </div>
                        </div>
                    @elseif($collab->status === 'rejected')
                        <div style="margin-top: 10px; font-size: 13px; font-weight: 700; color: #dc2626; display: flex; align-items: center; gap: 6px;">
                            <i class="bi bi-x-circle-fill"></i> You declined this request.
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
