<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-soft-gray-50 to-white py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('status') === 'profile-updated')
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl shadow-sm flex items-start gap-3">
                    <i class="bi bi-check-circle-fill text-green-600 text-xl flex-shrink-0 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="font-semibold">Success!</p>
                        <p class="text-sm">Your profile has been updated successfully.</p>
                    </div>
                </div>
            @endif


            {{-- Page Header --}}
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-3">
                    <div>
                        <h1 class="text-3xl font-bold text-soft-gray-900">Profile Settings</h1>
                        <p class="text-sm text-soft-gray-600 mt-1">Manage your account information and preferences</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                {{-- Profile Information Card --}}
                <div class="bg-white rounded-lg shadow-lg border border-soft-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-uco-orange-50 to-uco-yellow-50 px-6 py-4 border-b border-soft-gray-100">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-uco-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h2 class="text-lg font-bold text-soft-gray-900">Profile Information</h2>
                        </div>
                        <p class="text-xs text-soft-gray-600 mt-1">Update your account's profile information and email address</p>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                {{-- Update Password Card --}}
                <div class="bg-white rounded-lg shadow-lg border border-soft-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-uco-orange-50 to-uco-yellow-50 px-6 py-4 border-b border-soft-gray-100">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-uco-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <h2 class="text-lg font-bold text-soft-gray-900">Update Password</h2>
                        </div>
                        <p class="text-xs text-soft-gray-600 mt-1">Ensure your account is using a long, random password to stay secure</p>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for Profile Photo Preview --}}
    <script>    function validateAndPreviewPhoto(event) {
        const file = event.target.files[0];
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
        
        if (file && file.size > maxSize) {
            window.showToast('Profile photo must not be larger than 10MB. Please choose a smaller file.', 'error');
            event.target.value = '';
            return;
        }
        
        previewProfilePhoto(event);
    }
            function previewProfilePhoto(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('profile-photo-preview');
                    preview.innerHTML = `<img src="${e.target.result}" alt="Profile Photo" class="w-24 h-24 rounded-full object-cover border-4 border-gray-200 shadow-md">`;
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-app-layout>
