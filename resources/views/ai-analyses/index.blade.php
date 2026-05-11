<x-app-layout>
    <div class="space-y-6">
        {{-- Page Header --}}
        <section class="relative overflow-hidden rounded-xl border border-uco-orange-100 bg-white px-6 py-8 shadow-sm md:px-8 md:py-10">
            <div class="uco-hero-mesh"></div>
            <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="space-y-2 reveal-on-scroll">
                    <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-uco-orange-700">
                        Admin Dashboard
                    </span>
                    <h1 class="text-3xl font-extrabold text-soft-gray-900 md:text-4xl">Testimonial Review</h1>
                    <p class="text-sm text-soft-gray-600 mt-1">Review and manage UC-wide testimonies and AI moderation results.</p>
                </div>
            </div>
        </section>

        {{-- Stats Cards --}}
        @php
            $ucTotalCount = $ucAnalyses->total();
            $ucApprovedCount = \App\Models\UcAiAnalysis::where('is_approved', true)->count();
            $ucRejectedCount = \App\Models\UcAiAnalysis::where('is_approved', false)->count();
            $ucApprovalRate = $ucTotalCount > 0 ? round(($ucApprovedCount / $ucTotalCount) * 100, 1) : 0;
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
                <p class="text-xs font-medium text-gray-500 uppercase">Total Analyzed</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $ucTotalCount }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                <p class="text-xs font-medium text-gray-500 uppercase">Auto-Approved</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $ucApprovedCount }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
                <p class="text-xs font-medium text-gray-500 uppercase">Needs Review</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $rejectedCount }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
                <p class="text-xs font-medium text-gray-500 uppercase">Approval Rate</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $approvalRate }}%</p>
            </div>
        </div>

        {{-- UC Analyses Table --}}

        {{-- UC Analyses Table --}}
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Testimony</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Rating</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sentiment</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($ucAnalyses as $analysis)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="max-w-xs">
                                        <p class="text-sm font-medium text-gray-900">{{ $analysis->ucTestimony?->customer_name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-600 line-clamp-2 mt-1">{{ $analysis->ucTestimony?->content ?? '' }}</p>
                                        <p class="text-xs text-gray-500 mt-1">UC-wide</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $analysis->ucTestimony && $i <= $analysis->ucTestimony->rating ? 'text-yellow-400' : 'text-gray-300' }} fill-current" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $analysis->sentiment_score >= 70 ? 'bg-green-100 text-green-800' :
                                           ($analysis->sentiment_score >= 40 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ round($analysis->sentiment_score) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($analysis->is_approved)
                                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Approved
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Rejected
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-xs text-gray-500">
                                    {{ $analysis->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($analysis->ucTestimony)
                                            <a href="{{ route('uc-testimonies.detail', $analysis->ucTestimony) }}"
                                               class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-blue-600 hover:bg-blue-50 transition"
                                               title="View Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            
                                            {{-- Approve (if not approved) --}}
                                            @if(!$analysis->is_approved)
                                                <form method="POST" action="{{ route('uc-testimonies.approve', $analysis->ucTestimony) }}" 
                                                      onsubmit="return confirm('Approve this testimony?')"
                                                      class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-green-600 hover:bg-green-50 transition"
                                                            title="Approve">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Reject (always available) --}}
                                            <form method="POST" action="{{ route('uc-testimonies.reject', $analysis->ucTestimony) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="rejection_reason" value="">
                                                <button type="button"
                                                        onclick="
                                                            if (!confirm('Reject this testimony?')) return;
                                                            const reason = prompt('Optional reason for rejection:');
                                                            const input = this.closest('form').querySelector('input[name=rejection_reason]');
                                                            if (reason === null) {
                                                                input.value = 'Rejected by administrator';
                                                            } else {
                                                                input.value = reason;
                                                            }
                                                            this.closest('form').submit();
                                                        "
                                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-red-600 hover:bg-red-50 transition"
                                                        title="Reject">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <p class="text-gray-500 text-lg font-medium">No testimonies to moderate yet</p>
                                    <p class="text-sm text-gray-400 mt-1">testimonies will appear here automatically when submitted</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($ucAnalyses->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $ucAnalyses->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
