<x-app-layout bleed>
    @include('featured.partials.hero')

    <div class="page space-y-14">
        @include('featured.partials.ventures')

        <x-featured.people-section :entrepreneurs="$topEntrepreneurs" :intrapreneurs="$topIntrapreneurs" />
    </div>

    @include('featured.partials.testimonies')

    @guest
        <div class="page">
            <section class="card flex flex-col gap-4 !border-l-4 !border-l-brand-500 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="card-title">Do you study or did you graduate at UC Online Learning?</h2>
                    <p class="mt-1 text-base text-gray-700">Log in to add your venture or the company where you work.</p>
                </div>
                <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
            </section>
        </div>
    @endguest
</x-app-layout>
