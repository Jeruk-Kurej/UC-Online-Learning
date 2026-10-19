<x-app-layout bleed>
    @include('featured.partials.hero')

    <div class="page space-y-14">
        <x-featured.group :items="$featuredVentures" title="Our" accent="ventures" lead="Businesses run by our students and alumni." :href="route('businesses.index', ['view' => 'entrepreneur'])" link-label="See all ventures">
            @foreach ($featuredVentures as $venture)
                <x-featured.venture-tile :business="$venture" />
            @endforeach
        </x-featured.group>

        <x-featured.group :items="$topEntrepreneurs" title="Our" accent="entrepreneurs" lead="Students and alumni who run their own ventures.">
            @foreach ($topEntrepreneurs as $student)
                <x-featured.student-card :student="$student" type="entre" />
            @endforeach
        </x-featured.group>

        <x-featured.group :items="$topIntrapreneurs" title="Our" accent="intrapreneurs" lead="Students and alumni building their careers inside companies." :href="route('businesses.index', ['view' => 'intrapreneur'])" link-label="See all work profiles">
            @foreach ($topIntrapreneurs as $student)
                <x-featured.student-card :student="$student" type="intra" />
            @endforeach
        </x-featured.group>

        @if ($featuredVentures->isEmpty() && $topEntrepreneurs->isEmpty() && $topIntrapreneurs->isEmpty())
            <x-ui.empty-state title="Nothing to show yet">Ventures and people picked by the admin team will show here.</x-ui.empty-state>
        @endif
    </div>

    @include('featured.partials.testimonies')

    @guest
        <div class="page">
            <section class="flex flex-col gap-5 rounded-2xl bg-brand-500 p-8 text-ink sm:flex-row sm:items-center sm:justify-between sm:p-10">
                <div class="max-w-xl">
                    <h2 class="font-display text-2xl font-semibold leading-snug sm:text-3xl">Do you study or did you graduate at UC Online Learning?</h2>
                    <p class="mt-2 text-base">Log in to add your venture or the company where you work.</p>
                </div>
                <a href="{{ route('login') }}" class="btn shrink-0 bg-ink text-white hover:bg-black">Log in &raquo;</a>
            </section>
        </div>
    @endguest
</x-app-layout>
