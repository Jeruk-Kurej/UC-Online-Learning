{{-- --- Band on the campus photo under a dark layer; the photo is a real image so the browser fetches it first, and the dark colour shows if the file is missing --- --}}
<section {{ $attributes->class('relative isolate border-b-4 border-brand-500 bg-ink text-white') }}>
    <img src="{{ asset('images/hero-uc.webp') }}" alt="" width="800" height="450" fetchpriority="high" class="absolute inset-0 -z-20 h-full w-full object-cover">
    <div class="absolute inset-0 -z-10 bg-ink/85" aria-hidden="true"></div>
    {{ $slot }}
</section>
