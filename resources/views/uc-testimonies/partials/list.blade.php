<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6 justify-items-center">
    @forelse($users as $user)
        <div class="w-full max-w-[320px] bg-white border border-gray-100 rounded-[20px] overflow-hidden shadow-[0_20px_25px_-5px_rgba(0,0,0,0.05),0_10px_10px_-5px_rgba(0,0,0,0.01)] transition-all duration-300 hover:shadow-[0_30px_50px_rgba(0,0,0,0.08)] hover:-translate-y-1 flex flex-col relative"
             id="testimony-card-{{ $user->id }}">
            
            {{-- Status Badge (Top Left) --}}
            <div class="absolute top-4 left-4 z-20">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm backdrop-blur-md
                    {{ $user->is_visible ? 'bg-emerald-500/90 text-white' : 'bg-red-500/90 text-white' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                    {{ $user->is_visible ? 'Active' : 'Inactive' }}
                </span>
            </div>

            {{-- Featured Star Button (Top Right) --}}
            <div class="absolute top-4 right-4 z-20">
                <button type="button"
                        data-user-id="{{ $user->id }}"
                        data-featured="{{ $user->is_featured_testimony ? '1' : '0' }}"
                        onclick="toggleFeatured({{ $user->id }}, this)"
                        title="{{ !$user->is_visible ? 'User must be visible to be featured' : ($user->is_featured_testimony ? 'Remove from featured' : 'Add to featured') }}"
                        {{ !$user->is_visible ? 'disabled' : '' }}
                        class="w-10 h-10 rounded-full inline-flex items-center justify-center transition-all duration-300 border shadow-md focus:outline-none
                            {{ !$user->is_visible 
                                ? 'bg-gray-200/90 border-gray-300 text-gray-400 cursor-not-allowed'
                                : ($user->is_featured_testimony
                                    ? 'bg-[#ff8a00] border-[#ff8a00] text-white hover:bg-orange-600'
                                    : 'bg-white/90 border-gray-200 text-gray-400 hover:text-[#ff8a00] hover:border-[#ff8a00]') }}">
                    <i class="bi {{ $user->is_featured_testimony ? 'bi-star-fill' : 'bi-star' }} text-lg leading-none"></i>
                </button>
            </div>

            {{-- Top Section: Image & Info --}}
            <div class="relative h-[300px] w-full flex-shrink-0">
                @if($user->profile_photo_url)
                    <img src="{{ $user->profile_photo_url }}" 
                         alt="{{ $user->name }}"
                         class="w-full h-auto">
                @else
                    <div class="w-full h-full flex items-center justify-center text-white text-4xl font-black"
                         style="background: linear-gradient(135deg, #f7931e, #fdb913);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                
                {{-- Overlay Gradient --}}
                <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.4) 40%, transparent 100%);"></div>
                
                {{-- Text Content on Image --}}
                <div style="position: absolute; bottom: 45px; left: 20px; right: 20px; color: white;">
                    <h3 style="font-size: 18px; font-weight: 900; margin-bottom: 2px; letter-spacing: -0.5px; line-height: 1.2;">{{ $user->name }}</h3>
                    <p style="color: #cbd5e1; font-size: 11px; font-weight: 600; margin-bottom: 2px;">
                        {{ $user->current_status ?? 'Member' }} at UCO Community
                    </p>
                    <p style="color: #ff8a00; font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px;">
                        {{ $user->major ?? 'Student' }}
                    </p>
                </div>
            </div>

            {{-- Bottom Section: Testimony content --}}
            <div style="position: relative; padding: 35px 20px 25px 20px; text-align: center;" class="flex-grow flex items-center justify-center bg-white rounded-b-[20px]">
                {{-- Quote Icon --}}
                <div class="absolute -top-5 left-1/2 -translate-x-1/2 w-10 h-10 bg-uco-orange-500 rounded-xl shadow-[0_10px_15px_-3px_rgba(247,147,30,0.3)] flex items-center justify-center text-white z-10">
                    <i class="fa-solid fa-quote-left text-base"></i>
                </div>

                <p style="color: #334155; font-weight: 600; line-height: 1.6; font-size: 13px; font-style: italic; margin: 0;">
                    “{{ Str::limit($user->testimony, 200) }}”
                </p>
            </div>
        </div>
    @empty
        <div class="col-span-full py-16 text-center">
            <p class="text-gray-400 italic">No testimonies found.</p>
        </div>
    @endforelse
</div>

<div class="mt-8 pagination-ajax w-full">
    {{ $users->appends(request()->query())->links() }}
</div>
