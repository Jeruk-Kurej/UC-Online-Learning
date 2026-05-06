<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-3xl text-gray-900 leading-tight tracking-tight">
            {{ __('Command Center') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            
            {{-- Platform Health: 50mm Focal Point --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                @foreach([
                    ['label' => 'Total Members', 'value' => $stats['total_users'], 'icon' => 'bi-people-fill', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50'],
                    ['label' => 'Active Ventures', 'value' => $stats['total_businesses'], 'icon' => 'bi-rocket-takeoff-fill', 'color' => 'text-uco-orange-500', 'bg' => 'bg-orange-50'],
                    ['label' => 'Intrapreneurs', 'value' => $stats['total_companies'], 'icon' => 'bi-building-fill-check', 'color' => 'text-purple-600', 'bg' => 'bg-purple-50'],
                    ['label' => 'Awaiting Review', 'value' => $stats['pending_visibility'], 'icon' => 'bi-shield-exclamation', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50'],
                ] as $card)
                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-500 group relative overflow-hidden">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 {{ $card['bg'] }} opacity-20 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative z-10 space-y-4">
                            <div class="w-12 h-12 {{ $card['bg'] }} rounded-2xl flex items-center justify-center {{ $card['color'] }} text-xl border border-white shadow-sm">
                                <i class="bi {{ $card['icon'] }}"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">{{ $card['label'] }}</p>
                                <h3 class="text-4xl font-black text-gray-900 mt-1">{{ number_format($card['value']) }}</h3>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Secondary IA: Activity Streams --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                
                {{-- Recent Onboarding --}}
                <section class="space-y-6">
                    <div class="flex items-center justify-between px-2">
                        <h4 class="text-xs font-black uppercase tracking-widest text-gray-400">New Talent</h4>
                        <a href="{{ route('users.index') }}" class="text-[10px] font-black uppercase text-uco-orange-500 hover:tracking-widest transition-all">View All</a>
                    </div>
                    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm divide-y divide-gray-50 overflow-hidden">
                        @foreach($recentUsers as $user)
                            <div class="p-6 flex items-center gap-4 group hover:bg-gray-50/50 transition-colors">
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex-shrink-0 flex items-center justify-center text-gray-400 font-black text-xs border border-white">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 truncate">{{ $user->name }}</p>
                                    <p class="text-[10px] text-gray-500 font-medium">{{ $user->email }}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full bg-gray-100 text-[9px] font-black uppercase tracking-tighter text-gray-500">
                                    {{ $user->student_status ?? 'Student' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- Recent Ventures --}}
                <section class="space-y-6">
                    <div class="flex items-center justify-between px-2">
                        <h4 class="text-xs font-black uppercase tracking-widest text-gray-400">Latest Ventures</h4>
                        <a href="{{ route('businesses.index') }}" class="text-[10px] font-black uppercase text-uco-orange-500 hover:tracking-widest transition-all">Explore</a>
                    </div>
                    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm divide-y divide-gray-50 overflow-hidden">
                        @foreach($recentBusinesses as $business)
                            <div class="p-6 flex items-center gap-4 group hover:bg-gray-50/50 transition-colors">
                                <div class="w-10 h-10 rounded-xl bg-gray-50 flex-shrink-0 flex items-center justify-center border border-gray-100 overflow-hidden">
                                    @if($business->logo_url)
                                        <img src="{{ $business->logo_url }}" class="w-full h-full object-contain p-1">
                                    @else
                                        <i class="bi bi-building text-gray-300"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 truncate">{{ $business->name }}</p>
                                    <p class="text-[10px] text-gray-500 font-medium">{{ $business->category->name ?? 'Uncategorized' }}</p>
                                </div>
                                <a href="{{ route('businesses.edit', $business) }}" class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-gray-900 hover:text-white transition-all">
                                    <i class="bi bi-pencil-fill text-[10px]"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </section>

            </div>
        </div>
    </div>
</x-app-layout>