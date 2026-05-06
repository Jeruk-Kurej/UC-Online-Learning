<x-app-layout>
    <div class="w-full max-w-[1600px] mx-auto py-6 sm:py-10 px-4 sm:px-6 lg:px-8">
        {{-- Back Button & Header Section --}}
        <div class="mb-6">
            <a href="{{ route('ai-analyses.index') }}"
                class="group inline-flex items-center justify-center sm:justify-start gap-2.5 px-4 py-2.5 bg-white hover:bg-gray-900 border border-gray-200 hover:border-gray-900 text-gray-700 hover:text-white rounded-xl font-medium text-sm shadow-sm hover:shadow-md transition-all duration-200">
                <i class="bi bi-arrow-left text-base group-hover:-translate-x-0.5 transition-transform duration-200"></i>
                <span>Back</span>
            </a>
        </div>

        {{-- Hero Header --}}
        <section class="relative overflow-hidden rounded-3xl border border-uco-orange-100 bg-white px-6 py-8 shadow-sm md:px-8 md:py-10 mb-8">
            <div class="uco-hero-mesh"></div>
            <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="space-y-2 reveal-on-scroll">
                    <span class="inline-flex items-center rounded-full border border-soft-gray-200 bg-soft-gray-50 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-soft-gray-700">
                        Testimonial Review
                    </span>
                    <h1 class="text-3xl font-extrabold text-soft-gray-900 md:text-4xl">Testimony Details</h1>
                    <p class="text-sm text-soft-gray-600 mt-1">Review student & alumni feedback for Universitas Ciputra Online</p>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Content --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border text-gray-900 border-gray-200 rounded-2xl p-6 md:p-8 shadow-sm reveal-on-scroll" style="transition-delay: 100ms;">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-uco-orange-100 text-uco-orange-700 rounded-full flex items-center justify-center font-bold text-xl uppercase shrink-0">
                                {{ strtoupper(substr($testimony->customer_name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900">{{ $testimony->customer_name }}</h3>
                                <p class="text-sm text-gray-500">{{ optional($testimony->date)->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 text-uco-yellow-500 bg-uco-yellow-50 px-3 py-1.5 rounded-xl border border-uco-yellow-100 shrink-0">
                            <span class="font-bold text-uco-yellow-700 mr-1">{{ $testimony->rating }}.0</span>
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $testimony->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                        </div>
                    </div>
                    
                    <div class="prose prose-sm sm:prose-base max-w-none text-gray-700 bg-gray-50 p-5 rounded-xl border border-gray-100">
                        "{{ $testimony->content }}"
                    </div>
                </div>

                {{-- Moderation Context --}}
                <div class="bg-white border text-gray-900 border-gray-200 rounded-2xl p-6 shadow-sm reveal-on-scroll" style="transition-delay: 150ms;">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="bi bi-robot text-uco-orange-500"></i> Automated Review
                        </h3>
                        @if($analysis->is_approved)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <i class="bi bi-check-circle-fill"></i> Auto-Approved
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800 border border-red-200">
                                <i class="bi bi-exclamation-octagon-fill"></i> Auto-Rejected
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Sentiment Score</p>
                            <div class="flex items-baseline gap-2">
                                <p class="text-2xl font-bold text-gray-900">{{ number_format((float)$analysis->sentiment_score, 2) }}</p>
                                <span class="text-xs text-gray-500">/ 100</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                                <div class="{{ $analysis->sentiment_score >= 80 ? 'bg-emerald-500' : ($analysis->sentiment_score >= 50 ? 'bg-uco-yellow-500' : 'bg-red-500') }} h-1.5 rounded-full" style="width: {{ min(100, max(0, $analysis->sentiment_score)) }}%"></div>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Rejection Reason</p>
                            <p class="text-sm font-medium {{ $analysis->rejection_reason ? 'text-gray-900' : 'text-gray-400 italic' }}">
                                {{ $analysis->rejection_reason ?? 'No issues detected' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Admin Actions --}}
            <div class="lg:col-span-1 space-y-6">
                @if(auth()->user()?->isAdmin())
                    <div class="bg-gradient-to-br from-white to-gray-50 border border-gray-200 rounded-2xl p-6 shadow-sm sticky top-6 reveal-on-scroll" style="transition-delay: 200ms;">
                        <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-4 mb-5">Review Actions</h3>
                        
                        <p class="text-sm text-gray-600 mb-6">Review and make a final decision on this testimonial.</p>

                        <div class="space-y-3">
                            {{-- Approve --}}
                            @if(!$analysis->is_approved)
                                <form method="POST" action="{{ route('uc-testimonies.approve', $testimony) }}" 
                                      onsubmit="return confirm('Are you sure you want to approve this testimony for public display?')" class="block">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-md shadow-emerald-200 transition-all duration-200 active:scale-[0.98]">
                                        <i class="bi bi-check-lg"></i> Approve Testimony
                                    </button>
                                </form>
                            @endif

                            {{-- Reject (ask for optional reason) --}}
                            <form method="POST" action="{{ route('uc-testimonies.reject', $testimony) }}" class="block">
                                @csrf
                                <input type="hidden" name="rejection_reason" value="">
                                <button type="button" onclick="
                                    if (!confirm('Are you sure you want to reject this testimony? It will be hidden from the public.')) return;
                                    const reason = prompt('Optional reason for manual rejection:');
                                    if (reason === null) {
                                        this.closest('form').querySelector('input[name=rejection_reason]').value = 'Manually rejected by administrator';
                                    } else {
                                        this.closest('form').querySelector('input[name=rejection_reason]').value = reason;
                                    }
                                    this.closest('form').submit();
                                " class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold shadow-md shadow-red-200 transition-all duration-200 active:scale-[0.98]">
                                    <i class="bi bi-x-lg"></i> Reject Testimony
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.10, rootMargin: '0px 0px -40px 0px' });

                document.querySelectorAll('.reveal-on-scroll').forEach(target => observer.observe(target));
            });
        </script>
    @endpush
</x-app-layout>