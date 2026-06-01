@use('Illuminate\Support\Facades\Storage')

<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Profile Photo Upload -->
        <div class="space-y-4" x-data="{ 
            photoPreview: null,
            fileSelected(event) {
                const file = event.target.files[0];
                if(file) {
                    if(file.size > 10 * 1024 * 1024) {
                        window.showToast('Photo must not exceed 10MB', 'error');
                        this.$refs.photoInput.value = '';
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = (e) => { this.photoPreview = e.target.result; };
                    reader.readAsDataURL(file);
                }
            },
            clearPhoto() {
                this.photoPreview = null;
                this.$refs.photoInput.value = '';
            }
        }">
            <label class="block text-sm font-semibold text-gray-700">
                Profile Photo
            </label>
            <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                <!-- Photo Previews Area -->
                <div class="flex items-center gap-4 p-4 bg-gray-50 border border-gray-100 rounded-lg">
                    <!-- Current Photo -->
                    <div class="flex flex-col items-center gap-2">
                        <span class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">Current Photo</span>
                        @php $profilePhoto = $user->profile_photo_url; @endphp
                        <div>
                            @if($profilePhoto)
                                <img 
                                    src="{{ storage_image_url($profilePhoto, ['width' => 256, 'height' => 256, 'crop' => 'fit', 'quality' => 'auto', 'fetch_format' => 'auto']) }}?t={{ $user->updated_at?->timestamp ?? time() }}" 
                                    alt="Profile Photo" 
                                    class="w-24 h-24 rounded-full object-contain bg-slate-50 border-4 border-white shadow-sm"
                                />
                            @else
                                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-uco-orange to-uco-yellow flex items-center justify-center border-4 border-white shadow-sm">
                                    <span class="text-white text-3xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Arrow icon when there's a new photo -->
                    <template x-if="photoPreview">
                        <div class="flex flex-col items-center justify-center pt-5">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </div>
                    </template>

                    <!-- New Photo Preview -->
                    <template x-if="photoPreview">
                        <div class="flex flex-col items-center gap-2">
                            <span class="text-[10px] font-bold tracking-wider text-uco-orange uppercase">New Photo</span>
                            <div class="relative group">
                                <img :src="photoPreview" class="w-24 h-24 rounded-full object-contain bg-slate-50 border-4 border-uco-orange/30 shadow-md transition-all duration-300">
                                
                                <!-- Cancel/Remove Button -->
                                <button type="button" @click="clearPhoto" 
                                        class="absolute -top-1 -right-1 bg-red-500 hover:bg-red-600 text-white p-1.5 rounded-full shadow-lg transform transition-all hover:scale-110 focus:outline-none" 
                                        title="Cancel new photo">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Upload Actions -->
                <div class="flex-1 flex flex-col items-start gap-2">
                    <label for="profile_photo" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm focus-within:ring-2 focus-within:ring-uco-orange focus-within:border-uco-orange">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span x-text="photoPreview ? 'Change Selection' : 'Upload New Photo'"></span>
                        <input id="profile_photo" name="profile_photo" type="file" accept="image/jpeg, image/png, image/jpg, image/gif" class="sr-only" x-ref="photoInput" @change="fileSelected">
                    </label>

                    <x-input-error class="mt-1" :messages="$errors->get('profile_photo')" />
                </div>
            </div>
        </div>

        <!-- Name Field -->
        <div class="space-y-2">
            <label for="name" class="block text-sm font-semibold text-gray-700">
                Full Name
            </label>
            <input 
                id="name" 
                name="name" 
                type="text" 
                value="{{ old('name', $user->name) }}" 
                required 
                autofocus 
                autocomplete="name"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm transition-all duration-200 focus:border-uco-orange focus:ring-2 focus:ring-uco-orange/20 hover:border-gray-400"
                placeholder="Enter your full name"
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email Field -->
        <div class="space-y-2">
            <label for="email" class="block text-sm font-semibold text-gray-700">
                Email Address
            </label>
            <input 
                id="email" 
                name="email" 
                type="email" 
                value="{{ old('email', $user->email) }}" 
                required 
                autocomplete="username"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm transition-all duration-200 focus:border-uco-orange focus:ring-2 focus:ring-uco-orange/20 hover:border-gray-400"
                placeholder="your.email@example.com"
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold">⚠️ Email not verified.</span>
                        <button 
                            form="send-verification" 
                            class="ml-2 text-uco-orange hover:text-uco-orange/80 font-medium underline transition-colors"
                        >
                            Click here to resend verification
                        </button>
                    </p>


                </div>
            @endif
        </div>

        <!-- Username Field (if exists) -->
        @if(Schema::hasColumn('users', 'username'))
        <div class="space-y-2">
            <label for="username" class="block text-sm font-semibold text-gray-700">
                Username
            </label>
            <input 
                id="username" 
                name="username" 
                type="text" 
                value="{{ old('username', $user->username ?? '') }}" 
                autocomplete="username"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm transition-all duration-200 focus:border-uco-orange focus:ring-2 focus:ring-uco-orange/20 hover:border-gray-400"
                placeholder="Your unique username"
            />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
            <div class="flex items-center gap-4">
                <button 
                    type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors shadow-sm"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Changes
                </button>


            </div>
        </div>
    </form>
</section>

<script>
    function validateAndPreviewPhotoPartial(event) {
        const file = event.target.files[0];
        const maxSize = 10 * 1024 * 1024;

        if (file && file.size > maxSize) {
            window.showToast('Profile photo must not be larger than 10MB. Please choose a smaller file.', 'error');
            event.target.value = '';
            return;
        }

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('partial-new-photo-img');
                const wrapper = document.getElementById('partial-new-photo-wrapper');
                const arrow = document.getElementById('partial-preview-arrow');
                const cancelBtn = document.getElementById('partial-cancel-photo');

                img.src = e.target.result;
                wrapper.classList.remove('hidden');
                wrapper.classList.add('flex');
                arrow.classList.remove('hidden');
                arrow.classList.add('flex');
                cancelBtn.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    function cancelPartialPhoto() {
        document.getElementById('profile_photo').value = '';

        const wrapper = document.getElementById('partial-new-photo-wrapper');
        const arrow = document.getElementById('partial-preview-arrow');
        const cancelBtn = document.getElementById('partial-cancel-photo');

        wrapper.classList.add('hidden');
        wrapper.classList.remove('flex');
        arrow.classList.add('hidden');
        arrow.classList.remove('flex');
        cancelBtn.classList.add('hidden');
    }
</script>
