@use('Illuminate\Support\Facades\Storage')

<x-app-layout>
    <div class="max-w-[1600px] mx-auto">
        {{-- Page Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center gap-4">
            <a href="{{ route('businesses.show', $product->business) }}#products" 
               class="group inline-flex items-center justify-center sm:justify-start gap-2.5 px-4 py-2.5 bg-white hover:bg-gray-900 border border-gray-200 hover:border-gray-900 text-gray-700 hover:text-white rounded-xl font-medium text-sm shadow-sm hover:shadow-md transition-all duration-200">
                <i class="bi bi-arrow-left text-base group-hover:-translate-x-0.5 transition-transform duration-200"></i>
                <span>Back</span>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">Product Photos</h1>
                <p class="text-sm text-gray-600">{{ $product->name }} &mdash; {{ $product->business->name }}</p>
            </div>
            @if($photos->count() > 0)
                @auth
                    @if(auth()->id() === $product->business->user_id || auth()->user()->isAdmin())
                        <a href="{{ route('products.photos.create', $product) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white rounded-xl font-semibold text-sm shadow-sm transition duration-150">
                            <i class="bi bi-upload"></i>
                            Upload Photo
                        </a>
                    @endif
                @endauth
            @endif
        </div>

        {{-- Success / Error flash --}}

        {{-- Product Info Card --}}
        <div class="mb-6 bg-white border border-gray-200 shadow-sm rounded-xl p-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-orange-400 to-yellow-400 flex items-center justify-center text-white flex-shrink-0 shadow-sm">
                    <i class="bi bi-box-seam text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 truncate">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $product->productCategory->name }} &bull; Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xs text-gray-400 uppercase tracking-wider">Total Foto</p>
                    <p class="text-2xl font-bold text-orange-500">{{ $photos->count() }}</p>
                </div>
            </div>
        </div>

        {{-- Photos Grid --}}
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl">
            @if($photos->count() > 0)
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($photos as $photo)
                            @php $photoUrl = $photo->photo_url; @endphp

                            {{-- Each photo card with inline update panel --}}
                            <div x-data="{
                                    showUpdate: false,
                                    currentImage: '{{ $photoUrl ? storage_image_url($photoUrl) : '' }}',
                                    newImagePreview: null,
                                    newImageName: '',
                                    newImageSize: '',
                                    isDragging: false,
                                    fileSelected(event) {
                                        const file = event.target.files[0];
                                        if (file) {
                                            if (file.size > 10 * 1024 * 1024) {
                                                window.showToast('The photo is too large. Please select a file smaller than 10MB.', 'error');
                                                this.removeNewFile();
                                                return;
                                            }
                                            this.newImageName = file.name;
                                            this.newImageSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                                            const reader = new FileReader();
                                            reader.onload = (e) => { this.newImagePreview = e.target.result; };
                                            reader.readAsDataURL(file);
                                        }
                                    },
                                    removeNewFile() {
                                        this.newImagePreview = null;
                                        this.newImageName = '';
                                        this.newImageSize = '';
                                        if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                                    },
                                    cancelUpdate() {
                                        this.showUpdate = false;
                                        this.removeNewFile();
                                    },
                                    handleDrop(event) {
                                        this.isDragging = false;
                                        const file = event.dataTransfer.files[0];
                                        if (file) {
                                            this.$refs.fileInput.files = event.dataTransfer.files;
                                            this.fileSelected({ target: this.$refs.fileInput });
                                        }
                                    }
                                }"
                                class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200">

                                {{-- Photo Display --}}
                                <div class="relative group">
                                    @if($photoUrl)
                                        <div class="relative w-full h-52 overflow-hidden bg-slate-950/5 flex items-center justify-center">
                                            <!-- Blurred Background -->
                                            <img src="{{ storage_image_url($photoUrl, 'lqip') }}"
                                                 data-src="{{ storage_image_url($photoUrl, 'gallery_full') }}"
                                                 class="absolute inset-0 w-full h-full object-cover blur-xl opacity-40 scale-110 pointer-events-none transition duration-300 ease-out"
                                                 aria-hidden="true" referrerpolicy="no-referrer">
                                            <!-- Foreground Image -->
                                            <img src="{{ storage_image_url($photoUrl, 'lqip') }}"
                                                 data-src="{{ storage_image_url($photoUrl, 'gallery_full') }}"
                                                 alt="{{ $product->name }} photo"
                                                 loading="lazy"
                                                 class="relative z-10 max-w-full max-h-full object-contain blur-lg transition duration-300 ease-out"
                                                 referrerpolicy="no-referrer">
                                        </div>
                                    @else
                                        <div class="w-full h-52 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                            <i class="bi bi-image text-5xl text-gray-300"></i>
                                        </div>
                                    @endif

                                    {{-- Caption overlay --}}

                                    {{-- Hover actions (owner only) --}}
                                    @auth
                                        @if(auth()->id() === $product->business->user_id || auth()->user()->isAdmin())
                                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-200 rounded-t-2xl"></div>
                                            <div class="absolute top-2.5 right-2.5 flex gap-2 opacity-0 group-hover:opacity-100 transition-all duration-200">
                                                {{-- Update button --}}
                                                <button type="button"
                                                        @click="showUpdate = !showUpdate; if(!showUpdate) cancelUpdate()"
                                                        :class="showUpdate ? 'bg-blue-600 hover:bg-blue-700' : 'bg-white/90 hover:bg-white text-gray-700'"
                                                        class="inline-flex items-center justify-center w-9 h-9 rounded-full shadow-lg transition-all duration-150 backdrop-blur-sm"
                                                        :title="showUpdate ? 'Close update panel' : 'Update this photo'">
                                                    <template x-if="!showUpdate">
                                                        <i class="bi bi-pencil text-sm text-gray-700"></i>
                                                    </template>
                                                    <template x-if="showUpdate">
                                                        <i class="bi bi-x-lg text-sm text-white"></i>
                                                    </template>
                                                </button>

                                                {{-- Delete button --}}
                                                <form action="{{ route('products.photos.destroy', [$product, $photo]) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Delete this photo permanently?');"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center justify-center w-9 h-9 bg-red-500/90 hover:bg-red-600 text-white rounded-full shadow-lg transition-all duration-150 backdrop-blur-sm"
                                                            title="Delete photo">
                                                        <i class="bi bi-trash text-sm"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @endauth
                                </div>

                                {{-- Inline Update Panel --}}
                                @auth
                                    @if(auth()->id() === $product->business->user_id || auth()->user()->isAdmin())
                                        <div x-show="showUpdate"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 -translate-y-2"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             x-transition:leave="transition ease-in duration-150"
                                             x-transition:leave-start="opacity-100 translate-y-0"
                                             x-transition:leave-end="opacity-0 -translate-y-2"
                                             class="border-t border-blue-100 bg-blue-50/50">
                                            <form method="POST"
                                                  action="{{ route('products.photos.update', [$product, $photo]) }}"
                                                  enctype="multipart/form-data"
                                                  class="p-4 space-y-3">
                                                @csrf
                                                @method('PUT')

                                                {{-- Panel Header --}}
                                                <div class="flex items-center gap-2 mb-3">
                                                    <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center">
                                                        <i class="bi bi-pencil text-blue-600 text-xs"></i>
                                                    </div>
                                                    <p class="text-sm font-semibold text-blue-800">Update Photo</p>
                                                </div>

                                                {{-- Before / After Preview --}}
                                                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                                    {{-- Preview area --}}
                                                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-200/80 rounded-xl">
                                                        {{-- Current photo --}}
                                                        <div class="flex flex-col items-center gap-1">
                                                            <span class="text-[9px] font-bold tracking-widest text-gray-400 uppercase">Current</span>
                                                            <div class="w-16 h-16 rounded-lg bg-white border border-gray-200 flex items-center justify-center overflow-hidden shadow-sm">
                                                                <img :src="currentImage" alt="{{ $product->name . ' photo' }}" class="w-full h-full object-contain bg-slate-50">
                                                            </div>
                                                        </div>

                                                        {{-- Arrow (only when new selected) --}}
                                                        <template x-if="newImagePreview">
                                                            <div class="flex items-center justify-center px-0.5 pt-3">
                                                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                                                </svg>
                                                            </div>
                                                        </template>

                                                        {{-- New photo preview --}}
                                                        <template x-if="newImagePreview">
                                                            <div class="flex flex-col items-center gap-1">
                                                                <span class="text-[9px] font-bold tracking-widest text-blue-500 uppercase">New</span>
                                                                <div class="relative">
                                                                    <div class="w-16 h-16 rounded-lg bg-blue-50 border-2 border-blue-400 flex items-center justify-center overflow-hidden shadow-md">
                                                                        <img :src="newImagePreview" class="w-full h-full object-contain bg-slate-50">
                                                                    </div>
                                                                    <button type="button" @click="removeNewFile()"
                                                                            class="absolute -top-1.5 -right-1.5 bg-red-500 hover:bg-red-600 text-white p-0.5 rounded-full shadow-md transition-all hover:scale-110 focus:outline-none"
                                                                            title="Cancel new selection">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>

                                                    {{-- Upload button --}}
                                                    <div class="flex-1 flex flex-col gap-1.5"
                                                         @dragover.prevent="isDragging = true"
                                                         @dragleave.prevent="isDragging = false"
                                                         @drop.prevent="handleDrop($event)">
                                                        <label for="photo_{{ $photo->id }}"
                                                               :class="isDragging ? 'bg-blue-100 border-blue-400' : 'bg-white hover:bg-gray-50 border-gray-300'"
                                                               class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 border rounded-xl text-xs font-semibold text-gray-700 transition-all shadow-sm">
                                                            <i class="bi bi-upload text-base"></i>
                                                            <span x-text="newImagePreview ? 'Change Selection' : 'Select New Photo'"></span>
                                                            <input type="file"
                                                                   name="photo"
                                                                   id="photo_{{ $photo->id }}"
                                                                   accept="image/*"
                                                                   class="sr-only"
                                                                   x-ref="fileInput"
                                                                   @change="fileSelected">
                                                        </label>
                                                        <p class="text-[10px] text-gray-400">JPG, PNG, GIF (max. 10MB)</p>
                                                    </div>
                                                </div>

                                                {{-- File info --}}
                                                <div x-show="newImageName" class="flex items-center gap-2 text-xs text-blue-700 bg-blue-100 rounded-lg px-3 py-1.5">
                                                    <i class="bi bi-file-image text-blue-500"></i>
                                                    <span x-text="newImageName" class="truncate font-medium"></span>
                                                    <span x-text="'(' + newImageSize + ')'" class="text-blue-400 flex-shrink-0"></span>
                                                </div>

                                                {{-- Caption field --}}
                                                

                                                {{-- Action buttons --}}
                                                <div class="flex items-center justify-between pt-1">
                                                    <button type="button"
                                                            @click="cancelUpdate()"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 rounded-xl transition duration-150">
                                                        <i class="bi bi-x-lg text-xs"></i>
                                                        Batal
                                                    </button>
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition duration-150">
                                                        <i class="bi bi-check-lg"></i>
                                                        Simpan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                @endauth

                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-16 text-center">
                    <div class="w-20 h-20 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-images text-4xl text-orange-300"></i>
                    </div>
                    <p class="text-gray-500 text-lg font-semibold mb-1">No photos yet</p>
                    @auth
                        @if(auth()->id() === $product->business->user_id || auth()->user()->isAdmin())
                            <p class="text-sm text-gray-400 mb-5">Upload photos to showcase this product</p>
                            <a href="{{ route('products.photos.create', $product) }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold rounded-xl shadow-sm transition duration-150">
                                <i class="bi bi-upload"></i>
                                Upload First Photo
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </div>

    {{-- LQIP → full image swap via IntersectionObserver --}}
    <script>
        (function(){
            function loadImage(img){
                if(!img || !img.dataset || !img.dataset.src) return;
                if(img.dataset.loaded) return;
                img.onload = function(){
                    img.classList.remove('blur-lg');
                    img.dataset.loaded = '1';
                };
                img.src = img.dataset.src;
            }

            function observe(){
                var imgs = document.querySelectorAll('img[data-src]');
                if('IntersectionObserver' in window){
                    var io = new IntersectionObserver(function(entries){
                        entries.forEach(function(entry){
                            if(entry.isIntersecting){
                                loadImage(entry.target);
                                io.unobserve(entry.target);
                            }
                        });
                    },{rootMargin: '200px'});
                    imgs.forEach(function(i){ io.observe(i); });
                } else {
                    imgs.forEach(function(i){ loadImage(i); });
                }
            }

            if(document.readyState === 'loading'){
                document.addEventListener('DOMContentLoaded', observe);
            } else {
                observe();
            }
        })();
    </script>
</x-app-layout>