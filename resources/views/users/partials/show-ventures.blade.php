{{-- --- Profile page lists: businesses the person owns, businesses they help run, and workplaces --- --}}
@if($user->businesses->count() > 0)
    <div class="space-y-6">
        <x-people.section-heading start="Owned" end="Businesses" />
        <div class="grid grid-cols-1 gap-6">
            @foreach($user->businesses as $biz)
                <x-people.venture-card :href="route('businesses.show', $biz)" :name="$biz->name" :logo="$biz->logo_url">
                    @if($biz->category)
                        <span class="text-[9px] font-bold uppercase tracking-wider text-gray-500 mt-1 block">
                            {{ $biz->category->name }}
                        </span>
                    @endif
                    @if($biz->unique_value_proposition || $biz->description)
                        <p class="text-xs text-gray-500 font-normal leading-relaxed line-clamp-2 mt-2">
                            {{ $biz->unique_value_proposition ?? $biz->description }}
                        </p>
                    @endif
                    <x-slot:footer>
                        @if($biz->city)
                            <span class="flex items-center gap-1 text-[11px] font-bold text-gray-500">
                                <i class="bi bi-geo-alt-fill text-orange-500"></i>
                                {{ $biz->city }}
                            </span>
                        @endif
                    </x-slot:footer>
                </x-people.venture-card>
            @endforeach
        </div>
    </div>
@endif

@if($user->memberOfBusinesses->count() > 0)
    <div class="space-y-6">
        <x-people.section-heading start="Also" end="Manages" tone="blue" />
        <div class="grid grid-cols-1 gap-6">
            @foreach($user->memberOfBusinesses as $biz)
                <x-people.venture-card variant="member" :href="route('businesses.show', $biz)" :name="$biz->name" :logo="$biz->logo_url">
                    <span class="text-[9px] font-bold uppercase tracking-wider text-blue-600 mt-1 block">
                        {{ $biz->pivot->position ?? 'Co-Owner' }} (Team Member)
                    </span>
                    @if($biz->unique_value_proposition || $biz->description)
                        <p class="text-xs text-gray-500 font-normal leading-relaxed line-clamp-2 mt-2">
                            {{ $biz->unique_value_proposition ?? $biz->description }}
                        </p>
                    @endif
                    <x-slot:footer>
                        @if($biz->city)
                            <span class="flex items-center gap-1 text-[11px] font-bold text-gray-500">
                                <i class="bi bi-geo-alt-fill text-blue-500"></i>
                                {{ $biz->city }}
                            </span>
                        @endif
                    </x-slot:footer>
                </x-people.venture-card>
            @endforeach
        </div>
    </div>
@endif

@if($user->companies->count() > 0)
    <div class="space-y-6">
        <x-people.section-heading start="Employment" end="History" />
        <div class="grid grid-cols-1 gap-6">
            @foreach($user->companies as $company)
                <x-people.venture-card variant="company" :href="route('intrapreneurs.show', $company)" :name="$company->name" :logo="$company->logo_url" :logo-alt="$company->name">
                    @if($company->position)
                        <span class="text-[9px] font-bold uppercase tracking-wider text-gray-500 mt-1 block line-clamp-1">
                            {{ $company->position }}@if($company->level_position) · {{ $company->level_position }}@endif
                        </span>
                    @elseif($company->category)
                        <span class="text-[9px] font-bold uppercase tracking-wider text-gray-500 mt-1 block">
                            {{ $company->category->name }}
                        </span>
                    @endif
                    @if($company->job_description)
                        <p class="text-xs text-gray-500 font-normal leading-relaxed line-clamp-2 mt-2">
                            {{ $company->job_description }}
                        </p>
                    @endif
                    <x-slot:footer>
                        <div class="flex flex-wrap gap-x-4 gap-y-1 min-w-0">
                            @if($company->year_started_working)
                                <span class="flex items-center gap-1 text-[11px] font-bold text-gray-500">
                                    <i class="bi bi-calendar-check text-orange-500"></i>
                                    Since {{ $company->year_started_working }}
                                </span>
                            @endif
                            @if($company->company_scale)
                                <span class="text-[11px] font-bold text-gray-400 line-clamp-1" title="{{ $company->company_scale }}">
                                    {{ $company->company_scale }}
                                </span>
                            @endif
                        </div>
                    </x-slot:footer>
                </x-people.venture-card>
            @endforeach
        </div>
    </div>
@endif
