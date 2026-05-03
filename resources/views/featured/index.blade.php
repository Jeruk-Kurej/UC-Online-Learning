<x-app-layout>
    @section('title', 'Mahasiswa & Bisnis Unggulan UCO')

    <div class="space-y-16 pb-24 bg-white">
        {{-- Hero / Headline Section --}}
        <section class="group relative overflow-hidden rounded-3xl bg-gray-50 border border-gray-100 px-6 py-16 md:px-16 md:py-20 lg:px-24 mx-4 mt-4 shadow-sm">
            <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center">
                {{-- Left Content --}}
                <div class="space-y-6">
                    <div class="inline-flex items-center rounded-xl border border-gray-100 bg-white px-4 py-1.5 backdrop-blur-md shadow-sm">
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-800">
                            UCO Student Network
                        </span>
                    </div>

                    <div class="space-y-4">
                        <h1 class="text-3xl font-extrabold text-gray-900 md:text-5xl lg:text-6xl tracking-tight leading-tight">
                            Mahasiswa & Bisnis <span class="text-gray-600 block md:inline">Unggulan UCO</span>
                        </h1>
                        <p class="max-w-lg text-base font-normal leading-relaxed text-gray-500">
                            Menghubungkan inovasi mahasiswa dengan pasar nyata. Jelajahi bakat terbaik dalam jaringan entrepreneur dan intrapreneur UCO.
                        </p>
                    </div>

                    <div class="pt-4 flex flex-wrap gap-3">
                        <a href="{{ route('businesses.index', ['view' => 'entrepreneur']) }}"
                           class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-black hover:scale-[1.02]">
                            Explore Entrepreneur
                            <i class="bi bi-arrow-right text-sm"></i>
                        </a>
                        <a href="{{ route('businesses.index', ['view' => 'intrapreneur']) }}"
                           class="inline-flex items-center gap-2 rounded-xl bg-white border border-gray-200 px-6 py-3.5 text-sm font-bold text-gray-800 transition hover:bg-gray-50">
                            Explore Intrapreneur
                        </a>
                    </div>
                </div>

                {{-- Right Content: Spotlight Carousel/Grid --}}
                <div class="lg:pl-8">
                    <div class="grid grid-cols-2 gap-4">
                        @foreach ($spotlightBusinesses->take(4) as $index => $business)
                            <a href="{{ route('businesses.show', $business) }}"
                               class="block bg-white border border-gray-100 p-3 rounded-2xl hover:border-gray-200 hover:shadow-md transition duration-300">
                                <div class="relative aspect-video w-full overflow-hidden rounded-xl bg-gray-50">
                                    @if($business->logo_url)
                                        <img src="{{ $business->logo_url }}" class="h-full w-full object-contain p-2 hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-xs font-black text-gray-300">
                                            {{ strtoupper(substr($business->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="pt-3">
                                    <h4 class="text-sm font-bold text-gray-900 truncate tracking-tight hover:text-gray-700 transition">
                                        {{ $business->name }}
                                    </h4>
                                    <span class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">
                                        {{ $business->category->name ?? 'Business' }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- Best 3 Entrepreneur Students & Testimonies --}}
        <section class="max-w-[1600px] mx-auto px-4 md:px-8 space-y-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="space-y-2">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Top Entrepreneur Profiles</span>
                    <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-gray-800 rounded-full"></span>
                        Top 3 Entrepreneur Students
                    </h2>
                    <p class="text-sm text-gray-500 max-w-2xl font-normal leading-relaxed">
                        Pengakuan atas dampak nyata dari kontribusi mahasiswa di UCO terhadap bisnis mereka.
                    </p>
                </div>
                <a href="{{ route('users.index') }}" class="text-xs font-bold text-gray-600 hover:text-gray-900 transition flex items-center gap-1.5">
                    <span>View All Students</span>
                    <i class="bi bi-chevron-right text-[10px]"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($topEntrepreneurs as $student)
                    @php
                        $featuredBusiness = $student->businesses->first();
                    @endphp
                    <article class="group rounded-2xl border border-gray-100 bg-white p-6 shadow-sm hover:border-gray-200 hover:shadow-md transition duration-300 h-full flex flex-col justify-between">
                        <div class="space-y-4">
                            <div class="flex items-start gap-4">
                                <div class="h-14 w-14 overflow-hidden rounded-xl border border-gray-100 bg-gray-50 flex-shrink-0 flex items-center justify-center">
                                    @if($student->profile_photo_url)
                                        <img src="{{ $student->profile_photo_url }}" alt="{{ $student->name }}" class="h-full w-full object-cover">
                                    @else
                                        <span class="text-lg font-black text-gray-300">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <span class="text-[9px] font-black uppercase tracking-wider text-gray-400">Entrepreneur</span>
                                    <h3 class="mt-0.5 truncate text-lg font-bold text-gray-900 hover:text-gray-700 transition">
                                        <a href="{{ route('users.show', $student) }}">{{ $student->name }}</a>
                                    </h3>
                                    <p class="text-xs font-medium text-gray-500">{{ $student->major ?: 'UCO Student' }}</p>
                                </div>
                            </div>

                            @if($student->testimony)
                                <div class="relative bg-gray-50/50 rounded-xl p-4 border border-gray-100 text-gray-600 italic text-xs leading-relaxed">
                                    “{{ $student->testimony }}”
                                </div>
                            @endif

                            @if($featuredBusiness)
                                <a href="{{ route('businesses.show', $featuredBusiness) }}" class="block rounded-xl bg-gray-50 p-3 border border-gray-100 hover:border-gray-200 transition">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Founded Business</p>
                                    <p class="mt-0.5 text-xs font-bold text-gray-900 hover:text-gray-700">{{ $featuredBusiness->name }}</p>
                                    <p class="text-[10px] text-gray-400 line-clamp-1 mt-0.5">{{ $featuredBusiness->unique_value_proposition ?: 'Venture Highlight' }}</p>
                                </a>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full border border-dashed border-gray-200 p-8 rounded-2xl text-center text-gray-500 text-sm font-normal">
                        Belum ada mahasiswa entrepreneur yang ditampilkan.
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Top 5 Intrapreneur Students --}}
        <section class="max-w-[1600px] mx-auto px-4 md:px-8 space-y-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="space-y-2">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block">Top Intrapreneur Profiles</span>
                    <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-gray-800 rounded-full"></span>
                        Top 5 Intrapreneur Students
                    </h2>
                    <p class="text-sm text-gray-500 max-w-2xl font-normal leading-relaxed">
                        Mahasiswa dan alumni UCO yang membangun karir profesional luar biasa di perusahaan terkemuka.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                @forelse($topIntrapreneurs as $student)
                    @php
                        $featuredCompany = $student->companies->first();
                    @endphp
                    <article class="group rounded-2xl border border-gray-100 bg-white p-4 shadow-sm hover:border-gray-200 hover:shadow-md transition duration-300 h-full flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="h-11 w-11 overflow-hidden rounded-xl border border-gray-100 bg-gray-50 flex-shrink-0 flex items-center justify-center">
                                    @if($student->profile_photo_url)
                                        <img src="{{ $student->profile_photo_url }}" alt="{{ $student->name }}" class="h-full w-full object-cover">
                                    @else
                                        <span class="text-sm font-black text-gray-300">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <h3 class="truncate text-sm font-bold text-gray-900">
                                        <a href="{{ route('users.show', $student) }}" class="hover:text-gray-700 transition">{{ $student->name }}</a>
                                    </h3>
                                    <p class="text-[10px] font-medium text-gray-500 truncate">{{ $student->major ?: 'UCO Student' }}</p>
                                </div>
                            </div>

                            @if($featuredCompany)
                                <div class="rounded-xl bg-gray-50 p-2.5 border border-gray-100">
                                    <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">At Company</p>
                                    <p class="text-xs font-bold text-gray-900 line-clamp-1 uppercase">{{ $featuredCompany->name }}</p>
                                    <p class="text-[10px] text-gray-500 truncate mt-0.5">{{ $featuredCompany->position }}</p>
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full border border-dashed border-gray-200 p-8 rounded-2xl text-center text-gray-500 text-sm font-normal">
                        Belum ada mahasiswa intrapreneur yang ditampilkan.
                    </div>
                @endforelse
            </div>
        </section>

        {{-- CTA Login Section --}}
        <section class="max-w-[1600px] mx-auto px-4 md:px-8">
            <div class="rounded-2xl border border-gray-100 bg-white p-8 md:p-12 shadow-sm text-center flex flex-col items-center gap-4 transition hover:shadow-md duration-300">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Join our business ecosystem</span>
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 max-w-lg tracking-tight">
                    Are you a UCO Student and have a business?
                </h2>
                <p class="text-sm text-gray-500 max-w-md">
                    Add your business today and get discovered by our entire student and alumni network.
                </p>
                <div class="pt-2">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-3.5 bg-gray-900 hover:bg-black font-bold text-sm text-white rounded-xl transition duration-300">
                        <span>Add Your Business</span>
                        <i class="bi bi-plus-lg text-xs"></i>
                    </a>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
