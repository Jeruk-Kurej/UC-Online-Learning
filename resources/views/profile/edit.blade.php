<x-app-layout>
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ activeTab: 'basic' }">
        
        <div class="mb-10">
            <h1 class="text-3xl font-black text-gray-900">Edit Profile</h1>
            <p class="text-gray-500 font-medium">Keep your professional information up to date.</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Navigation --}}
            <div class="lg:w-64 flex flex-col gap-1">
                <button @click="activeTab = 'basic'" :class="activeTab === 'basic' ? 'bg-white shadow-sm border border-gray-200 text-gray-900 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 font-medium'" class="px-4 py-3 rounded-md text-left text-sm transition-all duration-200">
                    Basic Info
                </button>
                <button @click="activeTab = 'contact'" :class="activeTab === 'contact' ? 'bg-white shadow-sm border border-gray-200 text-gray-900 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 font-medium'" class="px-4 py-3 rounded-md text-left text-sm transition-all duration-200">
                    Contact
                </button>
                <button @click="activeTab = 'academic'" :class="activeTab === 'academic' ? 'bg-white shadow-sm border border-gray-200 text-gray-900 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 font-medium'" class="px-4 py-3 rounded-md text-left text-sm transition-all duration-200">
                    Academic
                </button>
                <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'bg-white shadow-sm border border-gray-200 text-gray-900 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 font-medium'" class="px-4 py-3 rounded-md text-left text-sm transition-all duration-200">
                    Security
                </button>
            </div>

            {{-- Form Content --}}
            <div class="flex-1 bg-white border rounded-xl p-10 shadow-sm">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-10">
                    @csrf
                    @method('PATCH')

                    {{-- Basic Tab --}}
                    <div x-show="activeTab === 'basic'" class="space-y-8">
                        <div class="flex flex-col sm:flex-row gap-8 items-center border-b pb-10">
                            <div class="relative group">
                                <div class="w-32 h-32 rounded-full border-4 border-gray-50 shadow-sm overflow-hidden bg-gray-100 flex items-center justify-center">
                                    @if($user->profile_photo_url)
                                        <img id="preview-image" src="{{ $user->profile_photo_url }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="bi bi-person text-5xl text-gray-300"></i>
                                    @endif
                                </div>
                                <label for="profile_photo" class="absolute bottom-0 right-0 w-10 h-10 bg-gray-900 text-white rounded-full flex items-center justify-center cursor-pointer hover:bg-black transition shadow-lg">
                                    <i class="bi bi-camera"></i>
                                    <input type="file" name="profile_photo" id="profile_photo" class="hidden" onchange="const [file] = this.files; if (file) document.getElementById('preview-image').src = URL.createObjectURL(file)">
                                </label>
                            </div>
                            <div class="flex-1 text-center sm:text-left">
                                <h3 class="text-lg font-black text-gray-900">Profile Photo</h3>
                                <p class="text-sm text-gray-500">JPG, PNG or GIF. Max size 2MB.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Full Name</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border-gray-200 rounded-lg px-5 py-3 focus:ring-uco-orange-500 focus:border-uco-orange-500">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Email Address</label>
                                <input type="email" value="{{ $user->email }}" class="w-full border-gray-200 rounded-lg px-5 py-3 bg-gray-50 text-gray-500 cursor-not-allowed" disabled>
                                <p class="text-[10px] text-gray-400">Email cannot be changed.</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Status</label>
                                <input type="text" value="{{ $user->display_status }}" class="w-full border-gray-200 rounded-lg px-5 py-3 bg-gray-50 text-gray-500 cursor-not-allowed" disabled>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Role</label>
                                <input type="text" value="{{ ucfirst($user->role) }}" class="w-full border-gray-200 rounded-lg px-5 py-3 bg-gray-50 text-gray-500 cursor-not-allowed" disabled>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Testimony</label>
                            <textarea name="testimony" rows="4" class="w-full border-gray-200 rounded-lg px-5 py-3 focus:ring-uco-orange-500 focus:border-uco-orange-500" placeholder="Share how UCO impacted your entrepreneurial journey...">{{ old('testimony', $user->testimony) }}</textarea>
                            <p class="text-[10px] text-gray-400">This may be displayed publicly on the homepage.</p>
                        </div>
                    </div>

                    {{-- Contact Tab --}}
                    <div x-show="activeTab === 'contact'" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">WhatsApp Number</label>
                                <input type="text" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}" placeholder="0812..." class="w-full border-gray-200 rounded-lg px-5 py-3 focus:ring-uco-orange-500 focus:border-uco-orange-500">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">LinkedIn Profile URL</label>
                                <input type="url" name="linkedin" value="{{ old('linkedin', $user->linkedin) }}" placeholder="https://linkedin.com/in/..." class="w-full border-gray-200 rounded-lg px-5 py-3 focus:ring-uco-orange-500 focus:border-uco-orange-500">
                            </div>
                        </div>
                    </div>

                    {{-- Academic Tab --}}
                    <div x-show="activeTab === 'academic'" class="space-y-8">
                        <p class="text-sm text-gray-400 italic">Academic information is managed by admin from the import data.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">NIS (Student ID)</label>
                                <input type="text" value="{{ $user->nis }}" class="w-full border-gray-200 rounded-lg px-5 py-3 bg-gray-50 text-gray-500 cursor-not-allowed" disabled>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Major / Peminatan</label>
                                <input type="text" value="{{ $user->major }}" class="w-full border-gray-200 rounded-lg px-5 py-3 bg-gray-50 text-gray-500 cursor-not-allowed" disabled>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Year of Enrollment</label>
                                <input type="text" value="{{ $user->year_of_enrollment }}" class="w-full border-gray-200 rounded-lg px-5 py-3 bg-gray-50 text-gray-500 cursor-not-allowed" disabled>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Graduate Year</label>
                                <input type="text" value="{{ $user->graduate_year ?? 'Active Student' }}" class="w-full border-gray-200 rounded-lg px-5 py-3 bg-gray-50 text-gray-500 cursor-not-allowed" disabled>
                            </div>
                        </div>
                    </div>

                    {{-- Security Tab --}}
                    <div x-show="activeTab === 'security'" class="space-y-8">
                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 text-sm text-blue-700">
                            <i class="bi bi-info-circle mr-1"></i> This password is only used to log in to this website.
                        </div>
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">New Password</label>
                                <input type="password" name="password" class="w-full border-gray-200 rounded-lg px-5 py-3 focus:ring-uco-orange-500 focus:border-uco-orange-500" placeholder="Minimum 8 characters">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="w-full border-gray-200 rounded-lg px-5 py-3 focus:ring-uco-orange-500 focus:border-uco-orange-500">
                            </div>
                            <p class="text-xs text-gray-400 italic">Leave empty to keep current password.</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-8 border-t border-gray-100">
                        <button type="button" onclick="history.back()" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-black transition shadow-sm">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
