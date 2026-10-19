<x-app-layout>
    @section('title', 'My businesses')

    <div class="space-y-12">
        <x-ui.page-header title="My businesses" subtitle="Your businesses and the places you work. New entries appear in the directory after the admin team approves them." />

        {{-- --- Businesses --- --}}
        <x-ui.section title="Businesses" lead="Businesses you own or run with others.">
            <x-slot:action>
                <a href="{{ route('businesses.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i>
                    Register a business
                </a>
            </x-slot:action>

            @if ($myBusinesses->isNotEmpty())
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($myBusinesses as $b)
                        <x-business.portfolio-card :href="route('businesses.show', $b)" :edit-href="route('businesses.edit', $b)" :item="$b" category-fallback="No category">
                            <x-slot:badges>
                                <x-business.status-pill :item="$b" />

                                @if ($b->is_featured && $b->status === 'approved')
                                    <span class="badge badge-info"><i class="bi bi-star" aria-hidden="true"></i> Featured on the home page</span>
                                @endif
                            </x-slot:badges>

                            <p class="line-clamp-3 text-base text-gray-700">{{ $b->description ?: 'No description yet.' }}</p>

                            @if ($b->hasReviewFeedback())
                                <x-business.review-feedback :item="$b" />
                            @endif
                        </x-business.portfolio-card>
                    @endforeach
                </div>
            @else
                <x-ui.empty-state title="No businesses yet">Register your business to show it to other students and alumni.</x-ui.empty-state>
            @endif
        </x-ui.section>

        {{-- --- Work profiles --- --}}
        <x-ui.section title="Work" lead="Companies where you work.">
            <x-slot:action>
                <a href="{{ route('intrapreneurs.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i>
                    Add a work profile
                </a>
            </x-slot:action>

            @if ($myCompanies->isNotEmpty())
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($myCompanies as $c)
                        <x-business.portfolio-card :href="route('intrapreneurs.show', $c)" :edit-href="route('intrapreneurs.edit', $c)" :item="$c" category-fallback="No industry">
                            <x-slot:badges>
                                <x-business.status-pill :item="$c" />
                            </x-slot:badges>

                            <dl class="divide-y divide-line border-y border-line">
                                @if ($c->position)
                                    <x-ui.fact label="Job title">{{ $c->position }}</x-ui.fact>
                                @endif
                                @if ($c->year_started_working)
                                    <x-ui.fact label="Since">{{ $c->year_started_working }}</x-ui.fact>
                                @endif
                            </dl>

                            @if ($c->hasReviewFeedback())
                                <x-business.review-feedback :item="$c" />
                            @endif
                        </x-business.portfolio-card>
                    @endforeach
                </div>
            @else
                <x-ui.empty-state title="No work profile yet">Add where you work so people can find you.</x-ui.empty-state>
            @endif
        </x-ui.section>
    </div>
</x-app-layout>
