<x-app-layout>
    @section('title', 'My Testimony')
    @push('styles')
        @vite('resources/css/pages/my-testimony.css')
    @endpush

    <div class="settings-page max-w-[1100px]">

        {{-- --- Header --- --}}
        <div class="mb-[30px]">
            <h1 class="settings-title">My Testimony</h1>
            <p class="settings-subtitle">Share your experience at UCO and help others grow.</p>
        </div>

        <div class="testimony-layout" x-data="{ testimony: @js($user->testimony ?? '') }">

            {{-- --- Preview card --- --}}
            <div class="testimony-preview">
                <div class="testimony-preview-card">
                    <div class="testimony-preview-photo">
                        <x-premium-image :src="$user->profile_photo_url" :alt="$user->name" class="w-full h-full" />

                        <div class="testimony-preview-shade"></div>

                        <div class="testimony-preview-info">
                            <h3 class="testimony-preview-name">{{ $user->name }}</h3>
                            <p class="testimony-preview-role">
                                {{ $user->current_status ?? 'Member' }} at UCO Community
                            </p>
                            <p class="testimony-preview-major">
                                {{ $user->major ?? 'Student' }}
                            </p>
                        </div>
                    </div>

                    <div class="testimony-preview-body">
                        <div class="absolute -top-6 left-1/2 -translate-x-1/2 w-12 h-12 bg-uco-orange-500 rounded-xl shadow-[0_10px_15px_-3px_rgba(247,147,30,0.3)] flex items-center justify-center text-white z-20">
                            <i class="fa-solid fa-quote-left text-lg"></i>
                        </div>

                        <p class="testimony-preview-text" x-text="testimony || 'Your testimony will appear here...'"></p>
                    </div>
                </div>
            </div>

            {{-- --- Form --- --}}
            <div class="flex-1">
                <div class="settings-card">
                    <form action="{{ route('uc-testimonies.store') }}" method="POST">
                        @csrf

                        <div class="mb-[25px]">
                            <label class="testimony-label">Your Testimony</label>
                            <textarea
                                name="content"
                                x-model="testimony"
                                class="testimony-input"
                                placeholder="What was your experience like at UCO?"
                                required
                            ></textarea>
                            <div class="testimony-counter-row">
                                <span class="testimony-counter" x-text="testimony.length + ' / 255'"></span>
                            </div>
                        </div>

                        <div class="testimony-actions">
                            <button type="submit" class="btn-uco btn-uco-primary">
                                <i class="bi bi-file-earmark-check-fill text-base"></i>
                                Save Testimony
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
