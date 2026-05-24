@php
    // configurable limit: pass $limit when including this component, default to 6
    $limit = $limit ?? 6;
    $total = $categories->count();
    $visibleTypes = $categories->take($limit);
    $moreCount = max(0, $total - $limit);
    $queryBase = request()->except('page');
@endphp

<div class="mb-4 px-6" x-data="{ modalOpen: false }">
    <div class="flex items-center gap-3 flex-wrap">
        <a href="{{ route('businesses.index') }}" class="px-3 py-1.5 rounded-full text-sm font-medium {{ request('type') ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-gray-900 text-white' }}" aria-current="{{ request('type') ? 'false' : 'true' }}">All</a>

        {{-- Visible limited set of types --}}
        @foreach($visibleTypes as $type)
            <a href="{{ route('businesses.index', array_merge($queryBase, ['type' => $type->id])) }}" class="px-3 py-1.5 rounded-full text-sm font-medium {{ request('type') == $type->id ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}" aria-pressed="{{ request('type') == $type->id ? 'true' : 'false' }}">
                {{ $type->name }}
            </a>
        @endforeach

        {{-- More button if there are additional types --}}
        @if($moreCount > 0)
            <button type="button" @click="modalOpen = true" class="px-3 py-1.5 rounded-full text-sm font-medium bg-white border border-gray-200 text-gray-700 hover:bg-gray-50">More ({{ $moreCount }})</button>
        @endif

        {{-- Quick link to full types page for admin/detail view --}}
        <a href="{{ route('business-types.index') }}" class="ml-2 text-xs text-gray-500 hover:text-gray-700">Manage types</a>
    </div>

    {{-- Modal listing all types --}}
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-40 flex items-start justify-center pt-20 px-4">
        <div class="fixed inset-0 bg-black/50" @click="modalOpen = false"></div>
        <div class="relative bg-white rounded-xl shadow-xl max-w-3xl w-full z-50 border border-gray-200">
            <div class="p-4 border-b flex items-center justify-between">
                <h3 class="text-lg font-semibold">Select a business type</h3>
                <button @click="modalOpen = false" class="text-gray-500 hover:text-gray-700">Close</button>
            </div>
            <div class="p-4 max-h-80 overflow-y-auto">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($categories as $type)
                        <a href="{{ route('businesses.index', array_merge($queryBase, ['type' => $type->id])) }}" @click="modalOpen = false" class="flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-100 hover:bg-gray-50 transition">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-yellow-400 flex items-center justify-center text-white font-semibold text-sm">{{ strtoupper(substr($type->name,0,1)) }}</div>
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-gray-900 truncate">{{ $type->name }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ Illuminate\Support\Str::limit($type->description ?? 'No description', 60) }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="p-4 border-t text-right">
                <a href="{{ route('business-types.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 rounded-lg text-sm text-gray-700 hover:bg-gray-200">View all types</a>
            </div>
        </div>
    </div>
</div>
