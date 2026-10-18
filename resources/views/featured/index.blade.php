<x-app-layout bleed>
    @include('featured.partials.hero')

    <div class="page space-y-14">
        @include('featured.partials.ventures')

        <x-featured.students-section :students="$topEntrepreneurs" type="entre" />

        <x-featured.students-section :students="$topIntrapreneurs" type="intra" />

        @include('featured.partials.testimonies')
    </div>
</x-app-layout>
