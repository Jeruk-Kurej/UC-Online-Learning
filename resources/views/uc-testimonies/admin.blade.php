<x-app-layout>
    @section('title', 'Manage Testimonies')

    <div class="testimonies-wrapper max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8"
        x-data="{
            isSubmitting: false,
            debounceTimer: null,
            init() {
                window.addEventListener('popstate', () => {
                    this.updateList(window.location.href, false);
                });
            },
            updateList(url = null, pushState = true, shouldScroll = false) {
                this.isSubmitting = true;
                if (!url) {
                    const form = this.$refs.filterForm;
                    if (!form) { console.error('filterForm ref not found!'); this.isSubmitting = false; return; }
                    const formData = new FormData(form);
                    const params = new URLSearchParams(formData);
                    url = `${form.action}?${params.toString()}`;
                }

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                })
                .then(res => {
                    if (!res.ok) { console.error('AJAX error:', res.status, res.url); }
                    return res.text();
                })
                .then(html => {
                    const container = document.getElementById('testimonies-list-container');
                    if (container) {
                        container.innerHTML = html;
                    }
                    if (pushState) window.history.pushState({}, '', url);
                    this.isSubmitting = false;
                    if (shouldScroll) {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                })
                .catch(err => {
                    console.error('Fetch failed:', err);
                    this.isSubmitting = false;
                });
            },
            resetFilters() {
                this.$refs.filterForm.reset();
                this.$refs.filterForm.querySelectorAll('input, select').forEach(el => el.value = '');
                this.updateList();
            },
            submitDebounced() {
                if (this.debounceTimer) clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => this.updateList(), 500);
            }
        }"
        @ajax-pagination.window="updateList($event.detail.url, true, true)">

        {{-- Page Header --}}
        <section class="relative overflow-hidden rounded-xl border border-gray-200 bg-white px-6 py-6 shadow-sm md:px-8 mb-8 reveal-on-scroll">
            <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div class="space-y-1">
                    <span class="inline-flex items-center rounded-md border border-uco-orange-200 bg-uco-orange-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-uco-orange-700">
                        Admin Portal
                    </span>
                    <h1 class="text-3xl font-extrabold text-soft-gray-900 md:text-4xl">Manage Testimonies</h1>
                    <p class="text-sm text-soft-gray-600 mt-1">Review user testimonies and select which ones are featured on the homepage.</p>
                </div>
            </div>
        </section>

        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll" style="transition-delay: 100ms;">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Total Testimonies</p>
                <p class="text-3xl font-bold text-gray-900" id="stat-total-testimonies">{{ $totalTestimonies }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll" style="transition-delay: 150ms;">
                <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wider mb-1">Featured Testimonies</p>
                <p class="text-3xl font-bold text-blue-600" id="stat-featured-testimonies">{{ $featuredTestimonies }}</p>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="mb-8 reveal-on-scroll" style="transition-delay: 300ms;">
            <form x-ref="filterForm" action="{{ route('uc-testimonies.admin') }}" method="GET" class="space-y-4"
                @submit.prevent="updateList()">
                
                {{-- Search & Filters Row --}}
                <div class="flex items-center gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-search text-gray-400"></i>
                        </div>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search user name or testimony content..."
                            @input="submitDebounced()"
                            @keydown.enter.prevent="updateList()"
                            class="w-full border-gray-300 bg-white rounded-md pl-10 pr-4 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm"
                        >
                    </div>

                    <div class="w-[115px] sm:w-48 flex-shrink-0">
                        <select id="featured" name="featured" @change="updateList()" class="w-full border-gray-300 bg-white rounded-md px-2 sm:px-3 py-2 text-xs sm:text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm">
                            <option value="">Status: All</option>
                            <option value="1" @selected(request('featured') === '1')>Featured</option>
                            <option value="0" @selected(request('featured') === '0')>Regular</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="resetFilters()" title="Reset Filters" class="inline-flex items-center justify-center bg-white border border-gray-300 text-gray-500 hover:text-gray-900 hover:bg-gray-50 h-[38px] w-[38px] rounded-md transition shadow-sm">
                            <i class="bi bi-arrow-clockwise text-lg"></i>
                        </button>
                        <div x-show="isSubmitting" x-cloak class="inline-flex items-center justify-center bg-uco-orange-50 border border-uco-orange-200 text-uco-orange-700 h-[38px] px-3 rounded-md shadow-sm">
                            <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.2" stroke-width="3"></circle>
                                <path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
                            </svg>
                            <span class="ml-2 text-xs font-medium hidden sm:inline">Updating...</span>
                        </div>
                    </div>
                </div>
            </form>
            <script>
                // Intercept pagination clicks
                document.addEventListener('click', function(e) {
                    const link = e.target.closest('.pagination-ajax a');
                    if (link) {
                        e.preventDefault();
                        window.dispatchEvent(new CustomEvent('ajax-pagination', {
                            detail: { url: link.href }
                        }));
                    }
                });
            </script>
        </div>

        {{-- Testimonies List Container --}}
        <div id="testimonies-list-container" :class="isSubmitting ? 'opacity-50 pointer-events-none transition-opacity' : 'transition-opacity'">
            @include('uc-testimonies.partials.list')
        </div>

    </div>

    @push('scripts')
    <script>
        function toggleFeatured(userId, buttonElement) {
            if (buttonElement.disabled) return;
            buttonElement.disabled = true;

            const token = document.querySelector('meta[name="csrf-token"]').content;
            const url = `/admin/testimonies/${userId}/toggle-featured`;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const isFeatured = data.is_featured;
                    buttonElement.setAttribute('data-featured', isFeatured ? '1' : '0');
                    buttonElement.title = isFeatured ? 'Remove from featured' : 'Add to featured';
                    
                    // Adjust classes
                    const icon = buttonElement.querySelector('i');
                    if (isFeatured) {
                        buttonElement.className = 'w-10 h-10 rounded-full inline-flex items-center justify-center transition-all duration-300 border shadow-md focus:outline-none bg-[#ff8a00] border-[#ff8a00] text-white hover:bg-orange-600';
                        icon.className = 'bi bi-star-fill text-lg leading-none';
                    } else {
                        buttonElement.className = 'w-10 h-10 rounded-full inline-flex items-center justify-center transition-all duration-300 border shadow-md focus:outline-none bg-white/90 border-gray-200 text-gray-400 hover:text-[#ff8a00] hover:border-[#ff8a00]';
                        icon.className = 'bi bi-star text-lg leading-none';
                    }
                    
                    // Update stats counters dynamically
                    const statFeatured = document.getElementById('stat-featured-testimonies');
                    if (statFeatured) {
                        let currentCount = parseInt(statFeatured.textContent, 10);
                        statFeatured.textContent = isFeatured ? currentCount + 1 : Math.max(0, currentCount - 1);
                    }

                    if (window.showToast) {
                        window.showToast(data.message, 'success');
                    }
                } else {
                    if (window.showToast) {
                        window.showToast(data.message || 'An error occurred.', 'error');
                    } else {
                        alert(data.message || 'An error occurred.');
                    }
                }
            })
            .catch(err => {
                console.error('Error toggling featured status:', err);
                const errMsg = err.message || 'An error occurred. Please try again.';
                if (window.showToast) {
                    window.showToast(errMsg, 'error');
                } else {
                    alert(errMsg);
                }
            })
            .finally(() => {
                buttonElement.disabled = false;
            });
        }
    </script>
    @endpush

</x-app-layout>
