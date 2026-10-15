<x-app-layout>
    <div data-featured-page class="space-y-16 pb-6">
        @include('featured.partials.hero')

        <x-featured.students-section :students="$topIntrapreneurs" type="intra" />

        <x-featured.students-section :students="$topEntrepreneurs" type="entre" />

        @include('featured.partials.ventures')

        @include('featured.partials.testimonies')
    </div>

</x-app-layout>
