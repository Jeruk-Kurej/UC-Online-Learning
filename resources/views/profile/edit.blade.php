<x-app-layout>
    {{-- UCO Profile Edit Page - V36: Clean Identity & Optimized Sidebar --}}
    <div style="max-width: 1000px; margin: 40px auto; padding: 0 25px; font-family: 'Inter', -apple-system, sans-serif; color: #1e293b;" x-data="{ activeTab: 'identity' }">
        
        {{-- Header --}}
        <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end;">
            <div>
                <h1 style="font-size: 30px; font-weight: 800; letter-spacing: -1.2px; margin: 0; color: #0f172a;">Profile Settings</h1>
                <p style="color: #64748b; font-size: 13px; margin-top: 4px; font-weight: 500;">Manage your personal information and academic records.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div style="display: flex; gap: 35px; align-items: flex-start;">
                
                {{-- Left Sidebar --}}
                <div style="width: 260px; flex-shrink: 0; position: sticky; top: 100px;">
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 30px; display: flex; flex-direction: column; align-items: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                        <div id="photo-container" style="width: 140px; height: 180px; border-radius: 10px; overflow: hidden; position: relative; background: #f8fafc; border: 1.5px solid #e2e8f0; margin-bottom: 20px; cursor: pointer; transition: 0.3s;" 
                             onmouseover="this.querySelector('.photo-overlay').style.opacity='1'" 
                             onmouseout="this.querySelector('.photo-overlay').style.opacity='0'">
                            
                            <img id="preview-image" src="{{ $user->profile_photo_url }}" style="width: 100%; height: 100%; object-fit: cover; {{ !$user->profile_photo_url ? 'display: none;' : '' }}">
                            
                            @if(!$user->profile_photo_url)
                                <div id="initials-placeholder" style="width: 100%; height: 100%; background: linear-gradient(135deg, #ff8a00, #ff4d00); display: flex; align-items: center; justify-content: center; color: white;">
                                    <span style="font-size: 48px; font-weight: 800; letter-spacing: -2px;">{{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}</span>
                                </div>
                            @endif

                            <label for="profile_photo" style="position: absolute; inset: 0; cursor: pointer; z-index: 20;">
                                <input type="file" name="profile_photo" id="profile_photo" style="display: none;" 
                                       accept="image/*"
                                       onchange="const [file] = this.files; if (file) { 
                                           const preview = document.getElementById('preview-image');
                                           const placeholder = document.getElementById('initials-placeholder');
                                           preview.src = URL.createObjectURL(file);
                                           preview.style.display = 'block';
                                           if(placeholder) placeholder.style.display = 'none';
                                       }">
                            </label>
                            <div class="photo-overlay" style="position: absolute; inset: 0; background: rgba(15,23,42,0.6); display: flex; flex-direction: column; align-items: center; justify-content: center; opacity: 0; transition: 0.3s ease; pointer-events: none; backdrop-filter: blur(2px);">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                                <span style="color: white; font-size: 9px; font-weight: 800; text-transform: uppercase; margin-top: 6px; letter-spacing: 0.5px;">Update</span>
                            </div>
                        </div>
                        <h3 style="font-size: 17px; font-weight: 800; text-align: center; margin: 0 0 4px 0; color: #0f172a; letter-spacing: -0.5px; line-height: 1.3;">{{ $user->prefix_title ? $user->prefix_title . ' ' : '' }}{{ $user->name }}{{ $user->suffix_title ? ', ' . $user->suffix_title : '' }}</h3>
                        <p style="font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 12px; text-align: center; word-break: break-all;">{{ $user->email }}</p>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                            <span style="padding: 3px 8px; background: #f1f5f9; border-radius: 5px; font-size: 9px; font-weight: 800; color: #475569; text-transform: uppercase;">{{ $user->student_status ?? 'Active' }}</span>
                        </div>
                        <div style="width: 100%; height: 1px; background: #f1f5f9; margin: 12px 0;"></div>
                        <p style="font-size: 11px; color: #94a3b8; text-align: center; line-height: 1.6; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Joined {{ $user->created_at->format('M Y') }}</p>
                    </div>
                </div>

                {{-- Main Form Sections --}}
                <div style="flex: 1; display: flex; flex-direction: column; gap: 30px;">
                    
                    {{-- Section 1: Identity --}}
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 35px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                        <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f1f5f9; letter-spacing: -0.5px;">Identity Information</h2>
                        
                        <div style="margin-bottom: 25px; display: flex; align-items: center; gap: 25px;">
                            <label style="width: 140px; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Full Name</label>
                            <div style="flex: 1; display: flex; gap: 10px;">
                                <input type="text" name="prefix_title" value="{{ old('prefix_title', $user->prefix_title) }}" placeholder="Prefix" style="width: 80px; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;">
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Full Name" style="flex: 1; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;">
                                <input type="text" name="suffix_title" value="{{ old('suffix_title', $user->suffix_title) }}" placeholder="Suffix" style="width: 90px; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;">
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 25px;">
                            <label style="width: 140px; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Personal Email</label>
                            <input type="email" name="personal_email" value="{{ old('personal_email', $user->personal_email) }}" style="flex: 1; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;">
                        </div>
                    </div>

                    {{-- Section 2: Contact --}}
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 35px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                        <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f1f5f9; letter-spacing: -0.5px;">Contact Details</h2>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                            <div><label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Phone Number</label><input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" style="width: 100%; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;"></div>
                            <div><label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Mobile Number</label><input type="text" name="mobile_number" value="{{ old('mobile_number', $user->mobile_number) }}" style="width: 100%; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;"></div>
                            <div><label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">WhatsApp</label><input type="text" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}" style="width: 100%; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;"></div>
                            <div><label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">LinkedIn URL</label><input type="url" name="linkedin" value="{{ old('linkedin', $user->linkedin) }}" style="width: 100%; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;"></div>
                        </div>
                    </div>

                    {{-- Section 3: Academic --}}
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 35px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                        <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f1f5f9; letter-spacing: -0.5px;">Academic Records</h2>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                            <div><label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Student ID (NIS)</label><div style="width: 100%; height: 44px; padding: 0 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 700; color: #64748b; display: flex; align-items: center; box-sizing: border-box;">{{ $user->nis }}</div></div>
                            <div style="position: relative;">
                                <label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Current Status</label>
                                <select name="current_status" style="width: 100%; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; color: #0f172a; outline: none; appearance: none; background: white; cursor: pointer; box-sizing: border-box;">
                                    <option value="Entrepreneur" {{ old('current_status', $user->current_status) == 'Entrepreneur' ? 'selected' : '' }}>Entrepreneur</option>
                                    <option value="Intrapreneur" {{ old('current_status', $user->current_status) == 'Intrapreneur' ? 'selected' : '' }}>Intrapreneur</option>
                                </select>
                                <div style="position: absolute; right: 15px; top: 41px; pointer-events: none;"><svg width="10" height="10" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.5 4.5L6 8L9.5 4.5" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                            </div>
                            <div><label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Enrollment Year</label><input type="text" name="year_of_enrollment" value="{{ old('year_of_enrollment', $user->year_of_enrollment) }}" style="width: 100%; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;"></div>
                            <div><label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Graduate Year</label><input type="text" name="graduate_year" value="{{ old('graduate_year', $user->graduate_year) }}" style="width: 100%; height: 44px; padding: 0 15px; border: 1.5px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box;"></div>
                        </div>
                        <div style="margin-top: 25px;"><label style="display: block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Major</label><div style="width: 100%; height: 44px; padding: 0 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 7px; font-size: 13px; font-weight: 700; color: #64748b; display: flex; align-items: center; box-sizing: border-box;">{{ $user->major }}</div></div>
                    </div>

                    {{-- Section 4: Extras --}}
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 35px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                        <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 25px 0; padding-bottom: 15px; border-bottom: 2px solid #f1f5f9; letter-spacing: -0.5px;">Additional Documents</h2>
                        
                        <div style="display: flex; align-items: center; gap: 25px;">
                            <label style="width: 140px; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Activities Doc</label>
                            <div style="flex: 1; display: flex; align-items: center; gap: 12px;">
                                <div style="flex: 1; position: relative;">
                                    <input type="file" name="activities_file" id="activities_file" style="position: absolute; opacity: 0; inset: 0; cursor: pointer; z-index: 10;" onchange="document.getElementById('act-name').innerText = this.files[0].name">
                                    <div style="height: 44px; border: 1.5px dashed #cbd5e1; border-radius: 7px; display: flex; align-items: center; padding: 0 15px; color: #64748b; font-size: 12px; font-weight: 500; background: #f8fafc;"><span id="act-name">Documentation file...</span></div>
                                </div>
                                @if($user->activities_doc_url) <a href="{{ $user->activities_doc_url }}" target="_blank" style="padding: 10px 15px; background: #f1f5f9; border-radius: 7px; color: #0f172a; font-size: 11px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 6px;">View</a> @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Action - Outside Flex Container to limit Sidebar --}}
            <div style="margin-top: 30px; margin-bottom: 50px; display: flex; justify-content: flex-end; align-items: center; max-width: 1000px;">
                <div style="width: 260px; margin-right: 35px; flex-shrink: 0;"></div> {{-- Spacer matching sidebar width --}}
                <button type="submit" style="flex: 1; background: #198754; color: white; padding: 14px 40px; border-radius: 12px; font-size: 15px; font-weight: 800; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 12px; transition: 0.3s; box-shadow: 0 10px 15px -3px rgba(25, 135, 84, 0.3);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H16L21 8V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21Z" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 21V13H7V21" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 3V8H15" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA') {
                event.preventDefault();
                event.target.blur();
            }
        });
    </script>
</x-app-layout>
