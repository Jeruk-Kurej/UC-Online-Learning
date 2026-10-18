<x-app-layout>
    <div class="mx-auto w-full max-w-[1600px] px-4 py-8 sm:px-6 lg:px-8 2xl:max-w-[1720px]">
        {{-- --- Page Header --- --}}
        <div class="reveal-on-scroll mb-12">
            <h1 class="mb-2 text-4xl font-bold text-gray-900">My Business & Career Portfolio</h1>
            <p class="max-w-3xl text-lg leading-relaxed text-gray-600">
                Manage and showcase your entrepreneurial ventures and professional employment profiles within the UCO community.
            </p>
        </div>

        {{-- --- Ventures (entrepreneurship) --- --}}
        <x-business.portfolio-section class="mb-16" icon="bi-shop" title="My Ventures (Entrepreneurship)" subtitle="Businesses you own, operate, or co-founded."
                                      :action-url="route('businesses.create')" action-label="Register New Business">
            @if ($myBusinesses->isNotEmpty())
                <div class="reveal-stagger-12 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($myBusinesses as $b)
                        <x-business.portfolio-card :href="route('businesses.show', $b)" :item="$b" category-fallback="Uncategorized">
                            <x-slot:badges>
                                <x-business.status-pill :item="$b" />

                                @if ($b->is_featured && $b->status === 'approved')
                                    <span class="flex items-center gap-1.5 rounded-lg bg-yellow-400 px-2.5 py-1 text-xs font-bold text-yellow-900 shadow-sm">
                                        <i class="bi bi-star-fill text-xs"></i> Featured
                                    </span>
                                @endif
                            </x-slot:badges>

                            <p class="my-4 line-clamp-3 flex-1 text-left text-sm italic leading-relaxed text-gray-600">
                                {{ $b->description ?: 'No description provided' }}
                            </p>

                            @if ($b->hasReviewFeedback())
                                <x-business.review-feedback :item="$b" />
                            @endif
                        </x-business.portfolio-card>
                    @endforeach
                </div>
            @else
                <x-business.portfolio-empty icon="bi-shop-window" title="No Ventures Registered">
                    You haven't registered any businesses yet. Start showcasing your ventures to the UCO community!
                </x-business.portfolio-empty>
            @endif
        </x-business.portfolio-section>

        {{-- --- Work profiles (intrapreneurship) --- --}}
        <x-business.portfolio-section icon="bi-briefcase" title="My Professional Career (Intrapreneurship)"
                                      subtitle="Companies and corporate environments where you are currently employed as a professional."
                                      :action-url="route('intrapreneurs.create')" action-label="Register Work Profile">
            @if ($myCompanies->isNotEmpty())
                <div class="reveal-stagger-12 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($myCompanies as $c)
                        <x-business.portfolio-card :href="route('intrapreneurs.show', $c)" :item="$c" category-fallback="Corporate">
                            <x-slot:badges>
                                <span class="flex items-center gap-1.5 rounded-lg bg-emerald-500 px-2.5 py-1 text-xs font-bold text-white shadow-sm">
                                    <i class="bi bi-check-circle-fill text-xs"></i>
                                    Active
                                </span>
                            </x-slot:badges>

                            <div class="my-4 flex-1 space-y-1.5 text-left">
                                <p class="flex items-center gap-1.5 text-sm font-bold text-slate-700">
                                    <i class="bi bi-person-badge text-gray-600"></i>
                                    <span>{{ $c->position }}</span>
                                </p>
                                @if ($c->year_started_working)
                                    <p class="flex items-center gap-1.5 text-xs font-semibold text-gray-600">
                                        <i class="bi bi-calendar-check text-gray-600"></i>
                                        <span>Started: {{ $c->year_started_working }}</span>
                                    </p>
                                @endif
                                @if ($c->company_scale)
                                    <p class="flex items-center gap-1.5 text-xs font-semibold text-gray-600">
                                        <i class="bi bi-diagram-3 text-gray-600"></i>
                                        <span>Scale: {{ $c->company_scale }}</span>
                                    </p>
                                @endif
                            </div>
                        </x-business.portfolio-card>
                    @endforeach
                </div>
            @else
                <x-business.portfolio-empty icon="bi-briefcase" title="No Work Profiles Registered">
                    Showcase where you currently work and make your professional career impact visible to the UCO community!
                </x-business.portfolio-empty>
            @endif
        </x-business.portfolio-section>
    </div>
</x-app-layout>
