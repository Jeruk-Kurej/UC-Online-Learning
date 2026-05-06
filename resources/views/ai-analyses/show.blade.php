<x-app-layout>
    <div class="w-full max-w-[1600px] mx-auto space-y-6">
        {{-- Back Button --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('ai-analyses.index') }}" 
               class="group inline-flex items-center gap-2.5 px-5 py-3 bg-white hover:bg-gray-900 border border-gray-200 hover:border-gray-900 text-gray-700 hover:text-white rounded-xl font-semibold text-base shadow-sm hover:shadow-md transition-all duration-200">
                <i class="bi bi-arrow-left text-lg group-hover:-translate-x-0.5 transition-transform duration-200"></i>
                <span>Back</span>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">Testimony Details</h1>
                <p class="text-sm text-gray-600">Review testimony and AI moderation results</p>
            </div>
        </div>

        {{-- Testimony Card --}}
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                Original Testimony
            </h2>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $testimony->customer_name }}</p>
                        <p class="text-xs text-gray-500">{{ $testimony->date->format('d F Y') }}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $testimony->rating ? 'text-yellow-400' : 'text-gray-300' }} fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        @endfor
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $testimony->content }}</p>
                </div>

                <div class="pt-3 border-t border-gray-200">
                    <p class="text-xs text-gray-500">
                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Business: 
                        <a href="{{ route('businesses.show', $testimony->business) }}" 
                           class="text-blue-600 hover:underline">
                            {{ $testimony->business->name }}
                        </a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Moderation Context --}}
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                AI Moderation Results (Google Gemini)
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                {{-- Sentiment Score --}}
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4">
                    <p class="text-xs font-medium text-blue-800 uppercase mb-1">Sentiment Score</p>
                    <p class="text-3xl font-bold text-blue-600">{{ round($analysis->sentiment_score) }}%</p>
                    <div class="mt-2 bg-white rounded-full h-2 overflow-hidden">
                        <div class="bg-blue-600 h-full transition-all duration-500" 
                             style="width: {{ $analysis->sentiment_score }}%"></div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="bg-gradient-to-br from-{{ $analysis->is_approved ? 'green' : 'red' }}-50 to-{{ $analysis->is_approved ? 'green' : 'red' }}-100 rounded-lg p-4">
                    <p class="text-xs font-medium text-{{ $analysis->is_approved ? 'green' : 'red' }}-800 uppercase mb-1">Status</p>
                    <p class="text-2xl font-bold text-{{ $analysis->is_approved ? 'green' : 'red' }}-600">
                        {{ $analysis->is_approved ? 'Approved' : 'Needs Review' }}
                    </p>
                    <div class="text-4xl text-{{ $analysis->is_approved ? 'green' : 'red' }}-300 mt-2">
                        {{ $analysis->is_approved ? '✅' : '⚠️' }}
                    </div>
                </div>

                {{-- Analysis Date --}}
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4">
                    <p class="text-xs font-medium text-purple-800 uppercase mb-1">Analyzed</p>
                    <p class="text-lg font-bold text-purple-600">{{ $analysis->created_at->format('d M Y') }}</p>
                    <p class="text-xs text-purple-700 mt-1">{{ $analysis->created_at->format('H:i:s') }}</p>
                </div>
            </div>

            {{-- Rejection Reason --}}
            @if(!$analysis->is_approved && $analysis->rejection_reason)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-yellow-800 mb-1">Rejection Reason</p>
                            <p class="text-sm text-yellow-700">{{ $analysis->rejection_reason }}</p>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- Business-level testimonies have been removed --}}
    </div>
</x-app-layout>
