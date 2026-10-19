{{-- --- Home hero: what the site is, a search box for the directory and one featured venture --- --}}
<section class="border-b-4 border-brand-500 bg-ink text-white">
    <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-12 sm:px-6 sm:py-16 lg:grid-cols-5 lg:gap-12 lg:px-8">
        <div class="lg:col-span-3">
            <h1 class="max-w-3xl font-display text-4xl font-semibold leading-tight sm:text-5xl">Find the businesses and careers of Universitas Ciputra Online Learning alumni</h1>
            <p class="mt-4 max-w-2xl text-lg text-gray-200">Look up a business or the person behind it, or see where our students and alumni work.</p>

            <form action="{{ route('businesses.index') }}" method="GET" role="search" class="mt-8 flex max-w-2xl flex-col gap-2 rounded-lg bg-white p-2 sm:flex-row">
                <label for="home-search" class="sr-only">Search the directory</label>
                <input id="home-search" type="search" name="search" placeholder="Business or owner name" autocomplete="off" class="min-h-[2.75rem] flex-1 rounded-md border-0 bg-white px-3 text-base text-gray-900 placeholder:text-gray-500 focus:ring-0">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>

            <p class="mt-5 text-base text-gray-200">
                Or browse the
                <a href="{{ route('businesses.index', ['view' => 'entrepreneur']) }}" class="font-medium text-white underline underline-offset-2">entrepreneurs</a>
                and the
                <a href="{{ route('businesses.index', ['view' => 'intrapreneur']) }}" class="font-medium text-white underline underline-offset-2">intrapreneurs</a>.
            </p>
        </div>

        @if ($heroVenture)
            <div class="lg:col-span-2">
                <p class="mb-3 text-sm font-medium text-brand-300">Featured venture</p>
                <x-featured.venture-tile :business="$heroVenture" large />
            </div>
        @endif
    </div>
</section>
