{{-- --- Home hero: what the site is and a search box for the directory; a photo of the campus sits behind it when public/images/hero-uc.jpg exists --- --}}
<section @class([
    'border-b-4 border-brand-500 bg-ink text-white',
    'hero-photo' => $heroHasPhoto,
])>
    <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6 sm:py-20 lg:px-8">
        <h1 class="max-w-3xl font-display text-4xl font-semibold leading-tight sm:text-5xl">Find the businesses and careers of the UC Online Learning <span class="inline-block rounded-[50%] border-[3px] border-brand-500 px-4 pb-1">community</span></h1>
        <p class="mt-4 max-w-2xl text-lg text-gray-100">Look up a <strong>business</strong> or the person behind it, or see where our students and alumni <strong>work</strong>.</p>

        <form action="{{ route('businesses.index') }}" method="GET" role="search" class="mt-8 flex max-w-2xl items-center gap-2 rounded-full border border-white/40 bg-white/10 p-1.5 pl-5 backdrop-blur-sm transition-colors focus-within:border-white focus-within:bg-white/20">
            <i class="bi bi-search text-lg text-white/80" aria-hidden="true"></i>
            <label for="home-search" class="sr-only">Search the directory</label>
            <input id="home-search" type="search" name="search" placeholder="Business or owner" autocomplete="off" class="min-h-[2.5rem] min-w-0 flex-1 appearance-none !border-0 !bg-transparent px-2 text-base text-white !shadow-none placeholder:text-white/75 focus:ring-0 focus-visible:outline-none [&::-webkit-search-cancel-button]:hidden">
            <button type="submit" class="btn bg-brand-500 text-ink hover:bg-brand-400">Search</button>
        </form>

        <p class="mt-5 text-base text-gray-100">
            Or browse the
            <a href="{{ route('businesses.index', ['view' => 'entrepreneur']) }}" class="font-medium text-white underline underline-offset-2">entrepreneurs</a>
            and the
            <a href="{{ route('businesses.index', ['view' => 'intrapreneur']) }}" class="font-medium text-white underline underline-offset-2">intrapreneurs</a>.
        </p>
    </div>
</section>
