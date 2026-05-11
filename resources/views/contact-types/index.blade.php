<x-app-layout>
    @php
        $getPlatformColor = function($name) {
            $name = strtolower($name);
            if (str_contains($name, 'whatsapp')) return ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'];
            if (str_contains($name, 'instagram')) return ['bg' => 'bg-pink-50', 'text' => 'text-pink-600'];
            if (str_contains($name, 'facebook')) return ['bg' => 'bg-blue-50', 'text' => 'text-blue-700'];
            if (str_contains($name, 'twitter') || str_contains($name, ' x ')) return ['bg' => 'bg-sky-50', 'text' => 'text-sky-500'];
            if (str_contains($name, 'email') || str_contains($name, 'gmail') || str_contains($name, 'surat')) return ['bg' => 'bg-red-50', 'text' => 'text-red-500'];
            if (str_contains($name, 'phone') || str_contains($name, 'telepon') || str_contains($name, 'mobile') || str_contains($name, 'hp')) return ['bg' => 'bg-blue-50', 'text' => 'text-blue-600'];
            if (str_contains($name, 'website') || str_contains($name, 'url') || str_contains($name, 'link')) return ['bg' => 'bg-gray-50', 'text' => 'text-gray-600'];
            if (str_contains($name, 'telegram')) return ['bg' => 'bg-sky-50', 'text' => 'text-sky-400'];
            if (str_contains($name, 'tiktok')) return ['bg' => 'bg-gray-100', 'text' => 'text-gray-900'];
            if (str_contains($name, 'linkedin')) return ['bg' => 'bg-blue-50', 'text' => 'text-blue-800'];
            if (str_contains($name, 'youtube')) return ['bg' => 'bg-red-50', 'text' => 'text-red-700'];
            return ['bg' => 'bg-orange-50', 'text' => 'text-orange-600'];
        };
    @endphp
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        {{-- Page Header --}}
        <section class="relative overflow-hidden rounded-xl border border-uco-orange-100 bg-white px-6 py-8 shadow-sm md:px-8 md:py-10 mb-8 reveal-on-scroll">
            <div class="uco-hero-mesh"></div>
            <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="space-y-2 reveal-on-scroll">
                    <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.2em] text-uco-orange-700">
                        Admin Dashboard
                    </span>
                    <h1 class="text-3xl font-extrabold text-soft-gray-900 md:text-4xl">Contact Type Management</h1>
                    <p class="text-sm text-soft-gray-600 mt-1">Manage available contact platforms and icons.</p>
                </div>

                <div class="flex items-center gap-3 relative z-10 reveal-on-scroll" style="transition-delay: 100ms;">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="/contact-types/create" 
                               class="inline-flex items-center gap-2 rounded-xl bg-uco-orange-500 px-5 py-3 text-sm font-semibold text-white shadow-md shadow-uco-orange-200 transition hover:-translate-y-0.5 hover:bg-uco-orange-600">
                                <i class="bi bi-plus-circle"></i>
                                Create Contact Type
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </section>

        {{-- Contact Types Table Card --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden reveal-on-scroll" style="transition-delay: 150ms;">
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3.5 text-center text-xs font-medium text-gray-600 uppercase tracking-wider w-[15%]">
                                Icon Preview
                            </th>
                            <th scope="col" class="px-4 py-3.5 text-left text-xs font-medium text-gray-600 uppercase tracking-wider w-[40%]">
                                Platform Name
                            </th>
                            <th scope="col" class="px-4 py-3.5 text-center text-xs font-medium text-gray-600 uppercase tracking-wider w-[20%]">
                                Usage Count
                            </th>
                            @auth
                                @if(auth()->user()->isAdmin())
                                    <th scope="col" class="px-4 py-3.5 text-center text-xs font-medium text-gray-600 uppercase tracking-wider w-[25%]">
                                        Actions
                                    </th>
                                @endif
                            @endauth
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($contactTypes as $type)
                            <tr class="hover:bg-gray-50 transition">
                                {{-- Icon Preview --}}
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center">
                                        @php $colors = $getPlatformColor($type->platform_name); @endphp
                                        <div class="w-12 h-12 rounded-lg {{ $colors['bg'] }} flex items-center justify-center {{ $colors['text'] }} text-xl shadow-sm border {{ str_replace('bg-', 'border-', $colors['bg']) }}">
                                            <i class="{{ $type->icon_class }}"></i>
                                        </div>
                                    </div>
                                </td>

                                {{-- Platform Name --}}
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $type->platform_name }}</div>
                                </td>

                                {{-- Usage Count --}}
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium
                                        {{ $type->business_contacts_count > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $type->business_contacts_count ?? 0 }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                @auth
                                    @if(auth()->user()->isAdmin())
                                        <td class="px-4 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('contact-types.show', $type) }}" 
                                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
                                                   title="View Details">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>

                                                @if(($type->business_contacts_count ?? 0) == 0)
                                                    <form action="{{ route('contact-types.destroy', $type) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('⚠️ Delete {{ $type->platform_name }}?\n\nThis action cannot be undone!');"
                                                          class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 transition-colors"
                                                                title="Delete Type">
                                                            <i class="bi bi-trash text-lg"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-300 cursor-not-allowed" 
                                                          title="Cannot delete - {{ $type->business_contacts_count }} contact(s) using this type">
                                                        <i class="bi bi-trash text-lg"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                @endauth
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="bi bi-telephone text-6xl text-gray-300 mb-3"></i>
                                        <p class="text-gray-500 text-lg font-medium">No contact types found</p>
                                        <p class="text-gray-400 text-sm">Create your first contact type to enable business contact methods</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($contactTypes->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $contactTypes->links() }}
                </div>
            @endif
        </div>

        {{-- Stats Summary --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-orange-500 hover:shadow-md transition reveal-on-scroll" style="transition-delay: 200ms;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Type</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $contactTypes->total() }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center">
                        <i class="bi bi-telephone text-2xl text-orange-400"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500 hover:shadow-md transition reveal-on-scroll" style="transition-delay: 250ms;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Contact</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $contactTypes->sum('business_contacts_count') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center">
                        <i class="bi bi-link-45deg text-2xl text-blue-400"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-emerald-500 hover:shadow-md transition reveal-on-scroll" style="transition-delay: 300ms;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Avg per Type</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $contactTypes->count() > 0 ? round($contactTypes->sum('business_contacts_count') / $contactTypes->count(), 1) : 0 }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center">
                        <i class="bi bi-bar-chart text-2xl text-emerald-400"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>