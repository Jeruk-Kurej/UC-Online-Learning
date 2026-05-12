<x-app-layout>
    @section('title', 'My Testimony')

    <div style="max-width: 1100px; margin: 40px auto; padding: 0 25px; font-family: 'Inter', -apple-system, sans-serif; color: #1e293b;">
        
        {{-- Header --}}
        <div style="margin-bottom: 30px;">
            <h1 style="font-size: 30px; font-weight: 800; letter-spacing: -1.2px; margin: 0; color: #0f172a;">My Testimony</h1>
            <p style="color: #64748b; font-size: 13px; margin-top: 4px; font-weight: 500;">Share your experience at UCO and help others grow.</p>
        </div>

        <div style="display: flex; gap: 40px; align-items: flex-start;" x-data="{ testimony: @js($user->testimony ?? '') }">
            
            {{-- Left Side: Preview Card (Matching Screenshot Style) --}}
            <div style="width: 380px; flex-shrink: 0;">
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05), 0 10px 10px -5px rgba(0,0,0,0.01);">
                    {{-- Top Section: Image & Info --}}
                    <div style="position: relative; height: 420px;">
                        <img src="{{ $user->profile_photo_url }}" 
                             style="width: 100%; height: 100%; object-fit: cover;">
                        
                        {{-- Overlay Gradient --}}
                        <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.4) 40%, transparent 100%);"></div>
                        
                        {{-- Text Content on Image --}}
                        <div style="position: absolute; bottom: 25px; left: 25px; right: 25px; color: white;">
                            <h3 style="font-size: 20px; font-weight: 900; margin-bottom: 4px; letter-spacing: -0.5px;">{{ $user->name }}</h3>
                            <p style="color: #cbd5e1; font-size: 12px; font-weight: 600; margin-bottom: 2px;">
                                {{ $user->current_status ?? 'Member' }} at UCO Community
                            </p>
                            <p style="color: #ff8a00; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px;">
                                {{ $user->major ?? 'Student' }}
                            </p>
                        </div>
                    </div>

                    {{-- Bottom Section: Testimony content --}}
                    <div style="position: relative; padding: 45px 30px 35px 30px; text-align: center;">
                        {{-- Quote Icon --}}
                        <div style="position: absolute; top: -25px; left: 50%; transform: translateX(-50%); width: 50px; height: 50px; background: #ff8a00; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(255, 138, 0, 0.3); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; z-index: 20;">
                            <i class="bi bi-quote"></i>
                        </div>

                        <p style="color: #334155; font-weight: 600; line-height: 1.7; font-size: 14px; font-style: italic; margin: 0;" x-text="testimony || 'Your testimony will appear here...'"></p>
                    </div>
                </div>
            </div>

            {{-- Right Side: Form Card (Matching Profile Styling) --}}
            <div style="flex: 1;">
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 35px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <form action="{{ route('uc-testimonies.store') }}" method="POST">
                        @csrf
                        
                        <div style="margin-bottom: 25px;">
                            <label style="display: block; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">Your Testimony</label>
                            <textarea 
                                name="content" 
                                x-model="testimony"
                                style="width: 100%; height: 220px; padding: 20px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-size: 14px; font-weight: 500; font-family: inherit; color: #1e293b; outline: none; background: #f8fafc; resize: none; line-height: 1.6; transition: 0.2s;"
                                onfocus="this.style.background='white'; this.style.borderColor='#ff8a00'; this.style.boxShadow='0 0 0 4px rgba(255, 138, 0, 0.1)';"
                                onblur="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';"
                                placeholder="What was your experience like at UCO?"
                                required
                            ></textarea>
                            <div style="margin-top: 8px; display: flex; justify-content: flex-end;">
                                <span style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;" x-text="testimony.length + ' / 255'"></span>
                            </div>
                        </div>

                        <div style="border-top: 1px solid #e2e8f0; padding-top: 25px; display: flex; justify-content: flex-end;">
                            <button type="submit" style="background: #198754; color: white; padding: 12px 30px; border-radius: 8px; font-size: 14px; font-weight: 800; border: none; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: 0.3s; box-shadow: 0 4px 12px rgba(25, 135, 84, 0.2);"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 15px rgba(25, 135, 84, 0.3)';"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(25, 135, 84, 0.2)';">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H16L21 8V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21Z" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 21V13H7V21" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 3V8H15" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Save Testimony
                            </button>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
</x-app-layout>
