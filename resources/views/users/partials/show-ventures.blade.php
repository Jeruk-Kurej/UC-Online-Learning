{{-- --- Profile page lists: businesses the person owns, businesses they help run, and workplaces --- --}}
@if ($user->businesses->isNotEmpty())
    <x-ui.section title="Businesses">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            @foreach ($user->businesses as $biz)
                <x-business.card :business="$biz" :href="route('businesses.show', $biz)" :show-owner="false" />
            @endforeach
        </div>
    </x-ui.section>
@endif

@if ($user->memberOfBusinesses->isNotEmpty())
    <x-ui.section title="Also runs">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            @foreach ($user->memberOfBusinesses as $biz)
                <x-business.card :business="$biz" :href="route('businesses.show', $biz)" :show-owner="false" :note="$biz->pivot->position ?? 'Co-owner'" />
            @endforeach
        </div>
    </x-ui.section>
@endif

@if ($user->companies->isNotEmpty())
    <x-ui.section title="Work">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            @foreach ($user->companies as $company)
                <x-business.card :business="$company" kind="intrapreneur" :href="route('intrapreneurs.show', $company)" :show-owner="false" :note="$company->year_started_working ? 'Since ' . $company->year_started_working : null" />
            @endforeach
        </div>
    </x-ui.section>
@endif
