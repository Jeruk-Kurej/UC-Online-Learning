<x-app-layout>
    {{-- UCO Inbox & Network Hub - Premium Tabbed Dashboard --}}
    <div style="max-width: 1200px; margin: 40px auto; padding: 0 25px; font-family: 'Inter', -apple-system, sans-serif; color: #1e293b;" 
         x-data="{ activeTab: 'messages' }">
        
        {{-- Header --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1 style="font-size: 30px; font-weight: 800; letter-spacing: -1.2px; margin: 0; color: #0f172a;">Inbox & Connections</h1>
                <p style="color: #64748b; font-size: 13px; margin-top: 4px; font-weight: 500;">Manage your collaboration invitations and connect with partners.</p>
            </div>
            <a href="{{ route('featured') }}" 
               style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 10px; background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; font-size: 13px; font-weight: 700; text-decoration: none; transition: 0.2s;"
               onmouseover="this.style.background='#f1f5f9'; this.style.borderColor='#cbd5e1';" onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                <i class="bi bi-arrow-left"></i> Back to Featured
            </a>
        </div>

        {{-- Tabs Selector with increased spacing and alignment --}}
        <div style="display: flex; gap: 32px; border-bottom: 2px solid #f1f5f9; margin-bottom: 35px; padding-bottom: 0;">
            <button @click="activeTab = 'messages'" 
                    :style="activeTab === 'messages' ? 'border-bottom: 3px solid #f97316; color: #0f172a; font-weight: 800;' : 'color: #64748b; font-weight: 600;'"
                    style="padding: 12px 4px; font-size: 15px; border: none; background: transparent; cursor: pointer; transition: all 0.2s; outline: none; margin-bottom: -2.5px; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-chat-left-text-fill" style="font-size: 15px;"></i> Inbox & Invitations
            </button>
            <button @click="activeTab = 'sent'" 
                    :style="activeTab === 'sent' ? 'border-bottom: 3px solid #f97316; color: #0f172a; font-weight: 800;' : 'color: #64748b; font-weight: 600;'"
                    style="padding: 12px 4px; font-size: 15px; border: none; background: transparent; cursor: pointer; transition: all 0.2s; outline: none; margin-bottom: -2.5px; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-send-fill" style="font-size: 15px;"></i> Sent Requests
            </button>
            <button @click="activeTab = 'connections'" 
                    :style="activeTab === 'connections' ? 'border-bottom: 3px solid #f97316; color: #0f172a; font-weight: 800;' : 'color: #64748b; font-weight: 600;'"
                    style="padding: 12px 4px; font-size: 15px; border: none; background: transparent; cursor: pointer; transition: all 0.2s; outline: none; margin-bottom: -2.5px; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-people-fill" style="font-size: 15px;"></i> My Network
            </button>
        </div>

        {{-- Tabs Content Wrapper with CSS Grid stacking to prevent double content / vertical stacking during transition --}}
        <div style="display: grid; grid-template-columns: 1fr; min-height: 550px; position: relative;">
            
            {{-- Tab 1: Messages / Inbox --}}
            <div x-show="activeTab === 'messages'"
                 style="grid-column: 1; grid-row: 1; width: 100%;"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2">
                @if($messages->isEmpty())
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 60px 30px; text-align: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                        <div style="width: 60px; height: 60px; background: #f8fafc; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; color: #94a3b8;">
                            <i class="bi bi-chat-left-text" style="font-size: 28px;"></i>
                        </div>
                        <h3 style="font-size: 16px; font-weight: 700; color: #334155; margin-bottom: 4px;">Your Inbox is Empty</h3>
                        <p style="color: #64748b; font-size: 13px; margin: 0;">When other users request a collaboration, it will show up here.</p>
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @foreach($messages as $message)
                            <a href="{{ route('inbox.show', $message) }}" 
                               style="display: block; text-decoration: none; background: white; border: 1px solid {{ $message->read_at ? '#e2e8f0' : '#bae6fd' }}; border-radius: 12px; padding: 20px; box-shadow: {{ $message->read_at ? 'none' : '0 4px 6px -1px rgba(14, 165, 233, 0.05)' }}; transition: 0.2s; border-left: 5px solid {{ $message->read_at ? '#cbd5e1' : '#0284c7' }};"
                               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 15px -3px rgba(0,0,0,0.05)';"
                               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='{{ $message->read_at ? 'none' : '0 4px 6px -1px rgba(14, 165, 233, 0.05)' }}';">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                    <h3 style="font-size: 15px; font-weight: 800; color: {{ $message->read_at ? '#334155' : '#0284c7' }}; margin: 0;">{{ $message->subject }}</h3>
                                    <span style="font-size: 11px; font-weight: 600; color: #94a3b8;">{{ $message->created_at->diffForHumans() }}</span>
                                </div>
                                <p style="color: #64748b; font-size: 13px; margin: 0 0 12px 0; line-height: 1.5;">{{ Str::limit(strip_tags($message->body), 150) }}</p>
                                @if($message->sender)
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        @if($message->sender->profile_photo_url)
                                            <img src="{{ $message->sender->profile_photo_url }}" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;">
                                        @else
                                            <div style="width: 24px; height: 24px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800; color: #475569;">{{ substr($message->sender->name, 0, 1) }}</div>
                                        @endif
                                        <span style="font-size: 12px; font-weight: 700; color: #475569;">From: {{ $message->sender->name }}</span>
                                        <span style="padding: 2px 6px; background: #f1f5f9; border-radius: 4px; font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase;">{{ $message->sender->current_status }}</span>
                                    </div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                    <div style="margin-top: 20px;">
                        {{ $messages->links() }}
                    </div>
                @endif
            </div>

            {{-- Tab 2: Sent Requests --}}
            <div x-show="activeTab === 'sent'" x-cloak
                 style="grid-column: 1; grid-row: 1; width: 100%;"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2">
                @if($sentCollabs->isEmpty())
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 60px 30px; text-align: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                        <div style="width: 60px; height: 60px; background: #f8fafc; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; color: #94a3b8;">
                            <i class="bi bi-send" style="font-size: 28px;"></i>
                        </div>
                        <h3 style="font-size: 16px; font-weight: 700; color: #334155; margin-bottom: 4px;">No Sent Requests</h3>
                        <p style="color: #64748b; font-size: 13px; margin: 0;">Explore the business directory and reach out to collaborate with other founders.</p>
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @foreach($sentCollabs as $collab)
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
                                <div style="display: flex; align-items: center; gap: 16px;">
                                    <a href="{{ route('users.show', $collab->recipient->id) }}" style="text-decoration: none;">
                                        @if($collab->recipient->profile_photo_url)
                                            <img src="{{ $collab->recipient->profile_photo_url }}" style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0;">
                                        @else
                                            <div style="width: 48px; height: 48px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 800; color: #94a3b8;">{{ substr($collab->recipient->name, 0, 1) }}</div>
                                        @endif
                                    </a>
                                    <div>
                                        <h3 style="font-size: 15px; font-weight: 800; color: #0f172a; margin: 0;">
                                            <a href="{{ route('users.show', $collab->recipient->id) }}" style="color: inherit; text-decoration: none;" onmouseover="this.style.color='#f97316'" onmouseout="this.style.color='#0f172a'">
                                                {{ $collab->recipient->name }}
                                            </a>
                                        </h3>
                                        <p style="color: #64748b; font-size: 12px; margin: 2px 0 4px 0; font-weight: 500;">
                                            {{ $collab->recipient->major ?? 'UC Student' }} • Sent {{ $collab->created_at->format('M d, Y') }}
                                        </p>
                                        @if($collab->recipient->businesses->first())
                                            <a href="{{ route('businesses.show', $collab->recipient->businesses->first()->slug) }}" style="font-size: 11px; font-weight: 700; color: #ea580c; display: flex; align-items: center; gap: 4px; text-decoration: none;">
                                                <i class="bi bi-building"></i>{{ $collab->recipient->businesses->first()->name }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <a href="{{ route('users.show', $collab->recipient->id) }}" 
                                       style="padding: 6px 12px; border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc; font-size: 12px; font-weight: 700; color: #475569; text-decoration: none; transition: 0.2s;"
                                       onmouseover="this.style.background='#f1f5f9'; this.style.borderColor='#cbd5e1';" onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                                        View Profile
                                    </a>
                                    @if($collab->status === 'pending')
                                        <span style="padding: 6px 12px; background: #fff7ed; border: 1px solid #ffedd5; border-radius: 20px; font-size: 12px; font-weight: 800; color: #d97706; text-transform: uppercase;">
                                            <i class="bi bi-hourglass-split mr-1.5 animate-spin"></i>Pending
                                        </span>
                                    @elseif($collab->status === 'accepted')
                                        <span style="padding: 6px 12px; background: #f0fdf4; border: 1px solid #dcfce7; border-radius: 20px; font-size: 12px; font-weight: 800; color: #16a34a; text-transform: uppercase;">
                                            <i class="bi bi-check-circle-fill mr-1.5"></i>Connected
                                        </span>
                                    @else
                                        <span style="padding: 6px 12px; background: #fef2f2; border: 1px solid #fee2e2; border-radius: 20px; font-size: 12px; font-weight: 800; color: #dc2626; text-transform: uppercase;">
                                            <i class="bi bi-x-circle-fill mr-1.5"></i>Declined
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
                 style="grid-column: 1; grid-row: 1; width: 100%;"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2">
                @if($connections->isEmpty())
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 60px 30px; text-align: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                        <div style="width: 60px; height: 60px; background: #f8fafc; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; color: #94a3b8;">
                            <i class="bi bi-people" style="font-size: 28px;"></i>
                        </div>
                        <h3 style="font-size: 16px; font-weight: 700; color: #334155; margin-bottom: 4px;">No Connections Yet</h3>
                        <p style="color: #64748b; font-size: 13px; margin: 0;">Connections are formed when collaboration requests are accepted. Start connecting now!</p>
                    </div>
                @else
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px;">
                        @foreach($connections as $conn)
                            @php
                                $partner = ($conn->sender_id === auth()->id()) ? $conn->recipient : $conn->sender;
                            @endphp
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                                        <a href="{{ route('users.show', $partner->id) }}" style="text-decoration: none;">
                                            @if($partner->profile_photo_url)
                                                <img src="{{ $partner->profile_photo_url }}" style="width: 56px; height: 56px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0;">
                                            @else
                                                <div style="width: 56px; height: 56px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; color: #94a3b8;">{{ substr($partner->name, 0, 1) }}</div>
                                            @endif
                                        </a>
                                        <div>
                                            <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0;">
                                                <a href="{{ route('users.show', $partner->id) }}" style="color: inherit; text-decoration: none;" onmouseover="this.style.color='#f97316'" onmouseout="this.style.color='#0f172a'">
                                                    {{ $partner->name }}
                                                </a>
                                            </h3>
                                            <p style="color: #64748b; font-size: 12px; margin: 2px 0 0 0; font-weight: 500;">{{ $partner->major }}</p>
                                            <span style="display: inline-block; margin-top: 6px; padding: 2px 6px; background: #fff7ed; border-radius: 4px; font-size: 9px; font-weight: 800; color: #c2410c; text-transform: uppercase;">{{ $partner->current_status }}</span>
                                        </div>
                                    </div>
                                    
                                    @if($partner->businesses->first())
                                        <div style="background: #f8fafc; border-radius: 8px; padding: 12px; margin-bottom: 16px;">
                                            <span style="font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 2px;">Venture / Business</span>
                                            <span style="font-size: 13px; font-weight: 700; color: #334155; display: block;">{{ $partner->businesses->first()->name }}</span>
                                            <span style="font-size: 11px; color: #64748b; display: block; margin-top: 1px;">{{ $partner->businesses->first()->city }}, {{ $partner->businesses->first()->province }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div style="display: flex; gap: 8px; border-top: 1px solid #f1f5f9; padding-top: 16px; margin-top: 10px;">
                                    @if($partner->show_contact_details)
                                        @if($partner->whatsapp)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $partner->whatsapp) }}" target="_blank" 
                                               style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 38px; border-radius: 8px; border: 1.5px solid #22c55e; background: #f0fdf4; color: #15803d; font-size: 12px; font-weight: 800; text-decoration: none; transition: 0.2s;"
                                               onmouseover="this.style.background='#bbf7d0'" onmouseout="this.style.background='#f0fdf4'">
                                                <i class="bi bi-whatsapp"></i>Chat WA
                                            </a>
                                        @endif
                                        @if($partner->personal_email)
                                            <a href="mailto:{{ $partner->personal_email }}" 
                                               style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 38px; border-radius: 8px; border: 1.5px solid #3b82f6; background: #eff6ff; color: #1d4ed8; font-size: 12px; font-weight: 800; text-decoration: none; transition: 0.2s;"
                                               onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                                                <i class="bi bi-envelope"></i>Email
                                            </a>
                                        @endif
                                    @else
                                        <span style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 4px; height: 38px; border-radius: 8px; background: #f1f5f9; color: #64748b; font-size: 11px; font-weight: 700; text-align: center; border: 1px dashed #cbd5e1;">
                                            <i class="bi bi-shield-lock-fill"></i>Contact Details Hidden
                                        </span>
                                    @endif
                                    
                                    @if($partner->businesses->first())
                                        <a href="{{ route('businesses.show', $partner->businesses->first()->slug) }}" 
                                           style="width: 38px; height: 38px; border-radius: 8px; border: 1.5px solid #e2e8f0; display: inline-flex; align-items: center; justify-content: center; color: #475569; background: white; text-decoration: none; transition: 0.2s;"
                                           onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1';" onmouseout="this.style.background='white'; this.style.borderColor='#e2e8f0';"
                                           title="View Business Profile">
                                            <i class="bi bi-arrow-right-short" style="font-size: 20px;"></i>
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
