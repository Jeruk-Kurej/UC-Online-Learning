<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumbs & Navigation --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('businesses.show', $business) }}" 
                   class="group flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-900 hover:border-gray-900 text-gray-500 hover:text-white transition-all duration-300 shadow-sm font-bold text-sm">
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
            {{-- Left column: Product Gallery --}}
            <div class="lg:col-span-2">
                {{-- Main Gallery Card --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                    @php
                        $galleryPhotos = $product->photos->map(function ($photo) {
                            $full = storage_image_url($photo->photo_url, 'gallery_full');
                            $thumb = storage_image_url($photo->photo_url, 'gallery_thumb');

                            if (!$full && !$thumb) {
                                return null;
                            }

                            return [
                                'full' => $full ?: $thumb,
                                'thumb' => $thumb ?: $full,
                            ];
                        })->filter()->values();

                        $photoUrls = $galleryPhotos->pluck('full')->values();
                        $thumbnailUrls = $galleryPhotos->pluck('thumb')->values();
                    @endphp

                    @if($photoUrls->count() > 0)
                        <div x-data="{ 
                            activePhotoIndex: 0,
                            photos: @js($photoUrls),
                            fullscreenOpen: false,
                            next() { this.activePhotoIndex = (this.activePhotoIndex + 1) % this.photos.length },
                            prev() { this.activePhotoIndex = (this.activePhotoIndex - 1 + this.photos.length) % this.photos.length }
                        }" class="p-4 sm:p-6 pb-2 sm:pb-4">
                            
                            {{-- Active Preview Container --}}
                            <div @click="fullscreenOpen = true" 
                                 class="relative aspect-[16/10] sm:aspect-video md:aspect-[21/9] lg:aspect-video max-h-[500px] rounded-lg overflow-hidden bg-gray-100 border border-gray-100 group shadow-inner cursor-zoom-in">
                                <img :src="photos[activePhotoIndex]" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                     alt="{{ $product->name }}">
                                
                                {{-- Navigation Overlays --}}
                                @if($photoUrls->count() > 1)
                                    <button @click="prev()" @click.stop
                                            class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/30 backdrop-blur-md text-white border border-white/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-black/50 hover:scale-110">
                                        <i class="bi bi-chevron-left text-xl"></i>
                                    </button>
                                    <button @click="next()" @click.stop
                                            class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/30 backdrop-blur-md text-white border border-white/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-black/50 hover:scale-110">
                                        <i class="bi bi-chevron-right text-xl"></i>
                                    </button>
                                @endif
                            </div>

                            {{-- Thumbnails Container --}}
                            @if($thumbnailUrls->count() > 1)
                                <div class="flex items-center gap-3 mt-4 sm:mt-6 overflow-x-auto pt-2 pb-4 scrollbar-hide px-1">
                                    @foreach($thumbnailUrls as $index => $thumbnailUrl)
                                        <button @click="activePhotoIndex = {{ $index }}"
                                                :class="activePhotoIndex === {{ $index }} ? 'ring-2 ring-uco-orange-500 ring-offset-2 scale-95 border-transparent shadow-lg' : 'border-gray-200 hover:border-uco-orange-300 opacity-60 hover:opacity-100'"
                                                class="relative flex-shrink-0 w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden border-2 transition-all duration-300">
                                            <img src="{{ $thumbnailUrl }}" 
                                                 class="w-full h-full object-cover"
                                                 alt="Thumbnail">
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Fullscreen Photo Modal --}}
                            <div x-show="fullscreenOpen" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 x-cloak 
                                 class="fixed inset-0 z-[110] flex items-center justify-center">
                                <div class="absolute inset-0 bg-black/95 backdrop-blur-sm" @click="fullscreenOpen = false"></div>
                                
                                <button type="button" @click="fullscreenOpen = false"
                                        class="absolute top-6 right-6 text-white/70 hover:text-white bg-white/10 hover:bg-white/20 rounded-xl p-3 z-10 transition-all duration-200">
                                    <i class="bi bi-x-lg text-2xl"></i>
                                </button>

                                <div class="relative max-h-[90vh] max-w-[95vw] flex items-center justify-center cursor-zoom-out" @click="fullscreenOpen = false">
                                    <img :src="photos[activePhotoIndex]" 
                                         @click.stop
                                         class="max-h-[90vh] max-w-[95vw] object-contain rounded-lg shadow-2xl transition-all duration-500">
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="aspect-video flex flex-col items-center justify-center p-12 bg-gray-50 text-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center mb-4 text-gray-300">
                                <i class="bi bi-image text-4xl"></i>
                            </div>
                            <h3 class="text-gray-900 font-bold mb-1">No Photos Available</h3>
                            <p class="text-gray-500 text-sm max-w-xs">There are no photos for this product yet.</p>
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
                                <i class="bi bi-tag text-uco-orange-500"></i>
                                <span class="text-sm font-semibold text-gray-500">Category</span>
                            </div>
                            <span class="text-sm font-bold text-gray-900 bg-gray-50 px-3 py-1 rounded-lg">
                                {{ $product->productCategory->name ?? 'Uncategorized' }}
                            </span>
                        </div>
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

                    {{-- Product Description - Integrated Here --}}
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
