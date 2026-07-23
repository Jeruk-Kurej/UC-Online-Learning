<x-app-layout>
    @section('title', 'Edit Page: ' . $page->title)

    @php
        $content = is_string($page->content_json) ? json_decode($page->content_json, true) : ($page->content_json ?? []);
    @endphp

    <div class="py-12 px-6 max-w-[1200px] mx-auto font-sans">
        <div class="flex items-center justify-between mb-8">
            <div>
                <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-uco-orange-600 mb-2">
                    CMS Manager
                </span>
                <h1 class="text-3xl font-black text-slate-900">Edit {{ $page->title }}</h1>
            </div>
            <a href="{{ route($page->slug) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-sm transition-all">
                View Public Page <i class="bi bi-box-arrow-up-right"></i>
            </a>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            @if($page->slug === 'about')
                {{-- Specialized Form for About Page CTA & Banner Content --}}
                <div class="space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h2 class="text-lg font-extrabold text-slate-900">About Page CTA Settings</h2>
                        <p class="text-xs text-slate-500 font-medium">Manage the call-to-action banner heading, subtitle, and button text displayed on the About page.</p>
                    </div>

                    <div class="space-y-2">
                        <label for="cta_heading" class="block text-xs font-bold uppercase tracking-wider text-slate-700">CTA Heading</label>
                        <input type="text" id="cta_heading" value="{{ $content['cta_heading'] ?? 'Ready to build your legacy?' }}" 
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-uco-orange-500 focus:ring-uco-orange-500 font-bold text-slate-900 text-sm">
                    </div>

                    <div class="space-y-2">
                        <label for="cta_subtitle" class="block text-xs font-bold uppercase tracking-wider text-slate-700">CTA Subtitle</label>
                        <textarea id="cta_subtitle" rows="3" 
                                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-uco-orange-500 focus:ring-uco-orange-500 font-medium text-slate-700 text-sm">{{ $content['cta_subtitle'] ?? 'Join the UCO community today and gain access to a world of entrepreneurial opportunities.' }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="primary_btn_text" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Primary Button Label</label>
                            <input type="text" id="primary_btn_text" value="{{ $content['primary_btn_text'] ?? 'Get Started Now' }}" 
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-uco-orange-500 focus:ring-uco-orange-500 font-bold text-slate-900 text-sm">
                            <p class="text-[11px] text-slate-400 font-medium">Links directly to Login/Register screen.</p>
                        </div>

                        <div class="space-y-2">
                            <label for="secondary_btn_text" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Secondary Button Label</label>
                            <input type="text" id="secondary_btn_text" value="{{ $content['secondary_btn_text'] ?? 'Explore Directory' }}" 
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-uco-orange-500 focus:ring-uco-orange-500 font-bold text-slate-900 text-sm">
                            <p class="text-[11px] text-slate-400 font-medium">Links directly to Business Directory screen.</p>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-sm font-bold text-slate-500 mb-6 uppercase tracking-wider">Page Content Editor</p>
                <div id="editorjs" class="prose max-w-none min-h-[400px] p-6 border border-slate-100 rounded-xl bg-slate-50"></div>
            @endif

            <div class="mt-8 flex justify-end">
                <button id="save-btn" class="px-8 py-3.5 bg-uco-orange-500 text-white font-extrabold text-sm rounded-xl hover:bg-uco-orange-600 transition-colors shadow-md shadow-uco-orange-500/20">
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    @if($page->slug !== 'about')
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script>
        <script src="https://cdn.jsdelivr.net/npm/@editorjs/image@latest"></script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pageSlug = @json($page->slug);
            let editor = null;

            if (pageSlug !== 'about') {
                try {
                    editor = new EditorJS({
                        holder: 'editorjs',
                        placeholder: 'Start writing your content here...',
                        tools: {
                            header: {
                                class: window.Header,
                                inlineToolbar: true,
                                config: { levels: [2, 3, 4], defaultLevel: 2 }
                            },
                            list: { class: window.NestedList || window.EditorjsList || window.List, inlineToolbar: true },
                            image: {
                                class: window.ImageTool || window.SimpleImage,
                                config: {
                                    endpoints: { byFile: '{{ route('pages.upload-image') }}' },
                                    additionalRequestHeaders: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                }
                            }
                        },
                        data: @json($content)
                    });
                } catch (e) {
                    console.error("EditorJS initialization failed:", e);
                }
            }

            document.getElementById('save-btn').addEventListener('click', () => {
                const btn = document.getElementById('save-btn');
                const originalText = btn.innerText;
                btn.innerText = 'Saving...';
                btn.disabled = true;

                let payloadPromise;

                if (pageSlug === 'about') {
                    payloadPromise = Promise.resolve({
                        cta_heading: document.getElementById('cta_heading').value,
                        cta_subtitle: document.getElementById('cta_subtitle').value,
                        primary_btn_text: document.getElementById('primary_btn_text').value,
                        secondary_btn_text: document.getElementById('secondary_btn_text').value,
                    });
                } else {
                    payloadPromise = editor.save();
                }

                payloadPromise.then((outputData) => {
                    fetch('{{ route('pages.update', $page->slug) }}', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            title: '{{ $page->title }}',
                            content_json: outputData
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        btn.innerText = 'Saved!';
                        btn.classList.replace('bg-uco-orange-500', 'bg-slate-800');
                        setTimeout(() => {
                            btn.innerText = originalText;
                            btn.classList.replace('bg-slate-800', 'bg-uco-orange-500');
                            btn.disabled = false;
                        }, 2000);
                    })
                    .catch(err => {
                        console.error('Saving failed: ', err);
                        alert('Error saving data.');
                        btn.innerText = originalText;
                        btn.disabled = false;
                    });
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
