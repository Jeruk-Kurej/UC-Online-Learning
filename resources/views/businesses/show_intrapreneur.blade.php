<x-app-layout>
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Breadcrumbs --}}
        <nav class="flex mb-8 text-sm font-medium" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('businesses.index') }}?view=intrapreneur" class="text-gray-400 hover:text-uco-orange-500 transition">Directory</a></li>
                <li class="flex items-center space-x-2">
                    <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
                    <span class="text-gray-900">{{ $company->name }}</span>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Company Info --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Hero Section --}}
                <div class="bg-white border rounded-xl p-8 shadow-sm overflow-hidden relative">
                    <div class="uco-hero-mesh opacity-30"></div>
                    <div class="relative z-10 flex flex-col md:flex-row gap-8 items-start">
                        <div class="w-32 h-32 md:w-40 md:h-40 bg-gray-50 rounded-lg flex items-center justify-center border shadow-sm flex-shrink-0">
                            @if($company->logo_url)
                                <img src="{{ $company->logo_url }}" class="w-full h-full object-contain">
                            @else
                                <i class="bi bi-building text-5xl text-gray-200"></i>
                            @endif
                        </div>
                        <div class="flex-1 space-y-4">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                        {{ $company->category->name ?? 'Company' }}
                                    </span>
                                    <span class="inline-flex items-center rounded-md bg-purple-50 px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider text-purple-700 ring-1 ring-inset ring-purple-700/10">
                                        Intrapreneur
                                    </span>
                                </div>
                                <h1 class="text-4xl font-extrabold text-gray-900">{{ $company->name }}</h1>
                                
                                @if($company->job_description)
                                    <p class="text-gray-500 mt-4">{{ $company->job_description }}</p>
                                @endif
                            </div>
                            
                            <div class="flex flex-wrap gap-4 text-sm mt-4">
                                @if($company->position)
                                    <div class="flex items-center gap-2 text-gray-600 bg-gray-50 px-3 py-1.5 rounded-full border">
                                        <i class="bi bi-person-badge text-uco-orange-500"></i>
                                        <span>{{ $company->position }}</span>
                                    </div>
                                @endif
                                @if($company->year_started_working)
                                    <div class="flex items-center gap-2 text-gray-600 bg-gray-50 px-3 py-1.5 rounded-full border">
                                        <i class="bi bi-calendar-check text-uco-orange-500"></i>
                                        <span>Started {{ $company->year_started_working }}</span>
                                    </div>
                                @endif
                                @if($company->company_scale)
                                    <div class="flex items-center gap-2 text-gray-600 bg-gray-50 px-3 py-1.5 rounded-full border">
                                        <i class="bi bi-graph-up-arrow text-uco-orange-500"></i>
                                        <span>{{ $company->company_scale }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Achievements Section --}}
                @if($company->achievement)
                    <div class="bg-white border rounded-xl p-8 shadow-sm">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="bi bi-trophy text-uco-orange-500"></i> Achievements & Contributions
                        </h2>
                        <div class="prose max-w-none text-gray-600">
                            {{ $company->achievement }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right Column: Student Info --}}
            <div class="space-y-8">
                {{-- UCO Student Card --}}
                @if($company->user)
                    <div class="bg-white border rounded-xl p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">UCO Student</h3>
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-uco-orange-100 bg-gray-50 flex items-center justify-center">
                                @if($company->user->profile_photo_url)
                                    <img src="{{ $company->user->profile_photo_url }}" class="w-full h-full object-cover">
                                @else
                                    <i class="bi bi-person text-3xl text-gray-200"></i>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-black text-gray-900 text-xl leading-tight">{{ $company->user->full_titled_name }}</h4>
                                <p class="text-sm text-gray-500">{{ $company->user->major }}</p>
                                <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-50 text-blue-600">
                                    Intrapreneur · {{ $company->user->display_status }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-4 border-t pt-6">
                            <div class="flex items-center gap-3">
                                <i class="bi bi-envelope text-gray-400"></i>
                                <span class="text-sm text-gray-700">{{ $company->user->email }}</span>
                            </div>
                            @if($company->user->whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $company->user->whatsapp) }}" target="_blank" class="flex items-center gap-3 hover:text-green-600 transition">
                                    <i class="bi bi-whatsapp text-green-500"></i>
                                    <span class="text-sm text-gray-700">{{ $company->user->whatsapp }}</span>
                                </a>
                            @endif
                            @if($company->user->linkedin)
                                <a href="{{ $company->user->linkedin }}" target="_blank" class="flex items-center gap-3 hover:text-blue-600 transition">
                                    <i class="bi bi-linkedin text-blue-600"></i>
                                    <span class="text-sm">Connect on LinkedIn</span>
                                </a>
                            @endif
                        </div>

                        {{-- Skills --}}
                        @if($company->user->skills->count() > 0)
                            <div class="mt-6 pt-6 border-t">
                                <p class="text-xs font-bold text-gray-400 uppercase mb-3 tracking-wide">Skills</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($company->user->skills as $skill)
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-md text-[10px] font-bold uppercase">{{ $skill->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
