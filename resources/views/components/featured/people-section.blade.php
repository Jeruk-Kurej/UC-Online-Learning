{{-- --- Featured students and alumni, entrepreneurs and intrapreneurs on two tabs --- --}}
@props(['entrepreneurs', 'intrapreneurs'])

<x-ui.section title="Featured people" lead="Students and alumni worth knowing." :href="route('businesses.index')" link-label="See the directory" x-data="{ tab: 'entre' }">
    <div class="tabs" role="tablist" aria-label="Featured people">
        <button type="button" class="tab" :class="{ 'tab-active': tab === 'entre' }" role="tab" :aria-selected="tab === 'entre'" @click="tab = 'entre'">Entrepreneurs</button>
        <button type="button" class="tab" :class="{ 'tab-active': tab === 'intra' }" role="tab" :aria-selected="tab === 'intra'" @click="tab = 'intra'">Intrapreneurs</button>
    </div>

    <div x-show="tab === 'entre'" role="tabpanel" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($entrepreneurs as $student)
            <x-featured.student-card :student="$student" type="entre" />
        @empty
            <x-ui.empty-state class="col-span-full" title="No featured entrepreneurs yet">People picked by the admin team will show here.</x-ui.empty-state>
        @endforelse
    </div>

    <div x-show="tab === 'intra'" x-cloak role="tabpanel" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($intrapreneurs as $student)
            <x-featured.student-card :student="$student" type="intra" />
        @empty
            <x-ui.empty-state class="col-span-full" title="No featured intrapreneurs yet">People picked by the admin team will show here.</x-ui.empty-state>
        @endforelse
    </div>
</x-ui.section>
