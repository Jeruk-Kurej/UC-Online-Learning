<x-app-layout>
    <div class="mx-auto w-full max-w-[1600px] px-4 py-8 sm:px-6 lg:px-8 2xl:max-w-[1720px]">
        {{-- --- Page Header --- --}}
        <div class="reveal-on-scroll mb-12">
            <h1 class="mb-2 text-4xl font-extrabold tracking-tight text-gray-900">My Business & Career Portfolio</h1>
            <p class="max-w-3xl text-lg leading-relaxed text-gray-500">
                Manage and showcase your entrepreneurial ventures and professional employment profiles within the UCO community.
            </p>
        </div>

        {{-- --- Ventures (entrepreneurship) --- --}}
        <x-business.portfolio-section class="mb-16" icon="bi-shop" title="My Ventures (Entrepreneurship)" subtitle="Businesses you own, operate, or co-founded."
                                      :action-url="route('businesses.create')" action-label="Register New Business">
            @if ($myBusinesses->count() > 0)
                <div class="reveal-stagger-12 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($myBusinesses as $b)
                        <x-business.portfolio-card :href="route('businesses.show', $b)" :item="$b" category-fallback="Uncategorized">
                            <x-slot:badges>
                                <span class="{{ $b->status === 'approved' ? 'bg-green-500 text-white' : ($b->status === 'rejected' ? 'bg-red-500 text-white' : 'bg-uco-orange-500 text-white') }} flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-[9px] font-black uppercase tracking-wider shadow-sm">
                                    <i class="bi {{ $b->status === 'approved' ? 'bi-check-circle-fill' : ($b->status === 'rejected' ? 'bi-x-circle-fill' : 'bi-hourglass-split') }} text-[9px]"></i>
                                    {{ $b->status_label }}
                                </span>

                                @if ($b->is_featured && $b->status === 'approved')
                                    <span class="flex items-center gap-1.5 rounded-lg bg-yellow-400 px-2.5 py-1 text-[9px] font-black uppercase tracking-wider text-yellow-900 shadow-sm">
                                        <i class="bi bi-star-fill text-[9px]"></i> Featured
                                    </span>
                                @endif
                            </x-slot:badges>

                            <p class="my-4 line-clamp-3 flex-1 text-left text-sm italic leading-relaxed text-gray-500">
                                {{ $b->description ?: 'No description provided' }}
                            </p>

                            @if (in_array($b->status, ['rejected', 'need_revision']) && $b->rejection_reason)
                                <div class="{{ $b->status === 'rejected' ? 'bg-red-50 border-red-100' : 'bg-blue-50 border-blue-100' }} mb-4 rounded-xl border p-3 text-left">
                                    <p class="{{ $b->status === 'rejected' ? 'text-red-600' : 'text-blue-600' }} mb-1 text-[10px] font-bold uppercase tracking-wider">
                                        {{ $b->status === 'rejected' ? 'Rejection Reason:' : 'Revision Feedback:' }}
                                    </p>
                                    <p class="{{ $b->status === 'rejected' ? 'text-red-700' : 'text-blue-700' }} text-xs italic">"{{ $b->rejection_reason }}"</p>
                                </div>
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
            @if ($myCompanies->count() > 0)
                <div class="reveal-stagger-12 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($myCompanies as $c)
                        <x-business.portfolio-card :href="route('intrapreneurs.show', $c)" :item="$c" category-fallback="Corporate">
                            <x-slot:badges>
                                <span class="flex items-center gap-1.5 rounded-lg bg-emerald-500 px-2.5 py-1 text-[9px] font-black uppercase tracking-wider text-white shadow-sm">
                                    <i class="bi bi-check-circle-fill text-[9px]"></i>
                                    Active
                                </span>
                            </x-slot:badges>

                            <div class="my-4 flex-1 space-y-1.5 text-left">
                                <p class="flex items-center gap-1.5 text-sm font-extrabold text-slate-700">
                                    <i class="bi bi-person-badge text-slate-400"></i>
                                    <span>{{ $c->position }}</span>
                                </p>
                                @if ($c->year_started_working)
                                    <p class="flex items-center gap-1.5 text-xs font-semibold text-slate-400">
                                        <i class="bi bi-calendar-check text-slate-400"></i>
                                        <span>Started: {{ $c->year_started_working }}</span>
                                    </p>
                                @endif
                                @if ($c->company_scale)
                                    <p class="flex items-center gap-1.5 text-xs font-semibold text-slate-400">
                                        <i class="bi bi-diagram-3 text-slate-400"></i>
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
