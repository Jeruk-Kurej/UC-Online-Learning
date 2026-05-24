<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumbs & Navigation --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('businesses.show', $business) }}" 
                   class="group flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-900 hover:border-gray-900 text-gray-700 hover:text-white transition-all duration-300 shadow-sm font-bold text-sm">
                    <i class="bi bi-arrow-left"></i>
                    Back
                </a>
                <div class="h-8 w-px bg-gray-200"></div>
                <div>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">{{ $product->name }}</h1>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @can('update', $business)
                    <a href="{{ route('businesses.products.edit', [$business, $product]) }}" 
                       class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border-2 border-gray-100 text-gray-700 rounded-lg font-black text-sm hover:border-gray-900 hover:text-gray-900 transition-all shadow-sm">
                        <i class="bi bi-pencil-square"></i>
                        Edit Product
                    </a>
                    <form action="{{ route('businesses.products.destroy', [$business, $product]) }}" 
                          method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this product?');"
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-50 text-red-600 rounded-lg font-black text-sm hover:bg-red-600 hover:text-white transition-all shadow-sm border border-red-100">
                            <i class="bi bi-trash3"></i>
                            Delete
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left column: Product Photo --}}
            <div class="lg:col-span-2">
                {{-- Main Photo Card --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                    @if($product->photo_url)
                        <div class="p-4 sm:p-6">
                            <div class="relative aspect-[16/10] sm:aspect-video rounded-lg overflow-hidden bg-gray-100 border border-gray-100 shadow-inner">
                                <img src="{{ $product->photo_url }}" 
                                     class="w-full h-full object-cover"
                                     alt="{{ $product->name }}">
                            </div>
                        </div>
                    @else
                        <div class="aspect-video flex flex-col items-center justify-center p-12 bg-gray-50 text-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center mb-4 text-gray-300">
                                <i class="bi bi-image text-4xl"></i>
                            </div>
                            <h3 class="text-gray-900 font-bold mb-1">No Photo Available</h3>
                            <p class="text-gray-500 text-sm max-w-xs">There is no photo for this product yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right column: Sidebar Details --}}
            <div class="space-y-6">
                {{-- Price & Specs Card --}}
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm overflow-hidden flex flex-col">
                    <div class="mb-6">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Price</span>
                        <p class="text-3xl font-black text-gray-900">
                            {{ $product->price ? 'Rp ' . number_format($product->price, 0, ',', '.') : 'Contact for Price' }}
                        </p>
                    </div>

                    <div class="space-y-4 pt-6 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <i class="bi bi-calendar-check text-gray-400"></i>
                                <span class="text-sm font-semibold text-gray-500">Listed Since</span>
                            </div>
                            <span class="text-sm font-bold text-gray-900">
                                {{ $product->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>

                    {{-- Product Description --}}
                    <div class="mt-8 pt-8 border-t border-gray-100">
                        <h2 class="text-sm font-black text-gray-900 mb-3 flex items-center gap-2">
                            <i class="bi bi-info-circle text-uco-orange-500"></i>
                            Product Description
                        </h2>
                        <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed font-medium">
                            {!! nl2br(e($product->description ?? 'No description provided.')) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
