<x-app-layout>
    <div class="space-y-6">
        {{-- Page Header --}}
        <section class="relative overflow-hidden rounded-3xl border border-uco-orange-100 bg-white px-6 py-8 shadow-sm md:px-8 md:py-10">
            <div class="uco-hero-mesh"></div>
            <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="space-y-2 reveal-on-scroll">
                    <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-uco-orange-700">
                        Admin Dashboard
                    </span>
                    <h1 class="text-3xl font-extrabold text-soft-gray-900 md:text-4xl">Testimonial Review</h1>
                    <p class="text-sm text-soft-gray-600 mt-1">Review and manage UC-wide testimonies.</p>
                </div>
            </div>
        </section>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
                <p class="text-xs font-medium text-gray-500 uppercase">Total Testimonies</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalCount }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                <p class="text-xs font-medium text-gray-500 uppercase">Visible</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $approvedCount }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
                <p class="text-xs font-medium text-gray-500 uppercase">Hidden</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $rejectedCount }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
                <p class="text-xs font-medium text-gray-500 uppercase">Visibility Rate</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $approvalRate }}%</p>
            </div>
        </div>

        {{-- Testimonies Table --}}
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User & Testimony</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">AI Analysis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($testimonies as $testimony)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="max-w-xl">
                                        <p class="text-sm font-medium text-gray-900">{{ $testimony->name }}</p>
                                        <p class="text-xs text-gray-600 mt-1">{{ $testimony->testimony }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <p class="text-xs text-gray-500 font-medium">Sentiment: <span class="text-gray-900">{{ $testimony->ai_sentiment ?? 'N/A' }}</span></p>
                                        <p class="text-xs text-gray-500 font-medium">Score: <span class="text-gray-900">{{ $testimony->ai_score ?? 'N/A' }}</span></p>
                                        @if($testimony->ai_rejection_reason)
                                            <p class="text-xs text-red-500 font-medium mt-1 leading-snug">Reason: <span class="text-red-700">{{ $testimony->ai_rejection_reason }}</span></p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($testimony->is_visible)
                                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Visible
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Hidden
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    {{ $testimony->submitted_at ? $testimony->submitted_at->format('d M Y') : $testimony->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-start gap-2">
                                        {{-- Toggle Visibility Action --}}
                                        <form action="{{ route('admin.testimonies.toggle', $testimony->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            
                                            @if($testimony->is_visible)
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-red-600 hover:bg-red-50 transition"
                                                        title="Reject / Hide">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            @else
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-green-600 hover:bg-green-50 transition"
                                                        title="Approve / Make Visible">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </form>

                                        {{-- View Details Action --}}
                                        <a href="{{ route('admin.testimonies.show', $testimony->id) }}" 
                                           class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-blue-600 hover:bg-blue-50 transition"
                                           title="View Details">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <p class="text-gray-500 text-lg font-medium">No testimonies to moderate yet</p>
                                    <p class="text-sm text-gray-400 mt-1">testimonies will appear here automatically when users submit them</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($testimonies->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $testimonies->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
