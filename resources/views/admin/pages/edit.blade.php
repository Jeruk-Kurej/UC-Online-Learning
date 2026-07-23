<x-app-layout>
    @section('title', 'Edit Page: ' . $page->title)

    @php
        $content = is_string($page->content_json) ? json_decode($page->content_json, true) : ($page->content_json ?? []);
    @endphp

    <div class="py-12 px-6 max-w-[1200px] mx-auto font-sans">
        <div class="flex items-center justify-between mb-8">
            <div>
                <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-uco-orange-600 mb-2">
                    Visual CMS Block Manager
                </span>
                <h1 class="text-3xl font-black text-slate-900">Edit {{ $page->title }}</h1>
                <p class="text-xs text-slate-500 font-medium mt-1">Add, edit, remove, reorder text, headings, images, video embeds, and custom HTML blocks.</p>
            </div>
            <a href="{{ route($page->slug) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-sm transition-all">
                View Public Page <i class="bi bi-box-arrow-up-right"></i>
            </a>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <span class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Block Canvas Editor</span>
                <span class="text-xs text-slate-400 font-medium">Click <span class="font-bold text-slate-700">+</span> inside the editor to insert new blocks (Heading, Text, Image, Embed, Raw HTML, Quote)</span>
            </div>

            <div id="editorjs" class="prose max-w-none min-h-[500px] p-6 border border-slate-200 rounded-xl bg-slate-50/50"></div>

            <div class="mt-8 flex justify-end">
                <button id="save-btn" class="px-8 py-3.5 bg-uco-orange-500 text-white font-extrabold text-sm rounded-xl hover:bg-uco-orange-600 transition-colors shadow-md shadow-uco-orange-500/20">
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/image@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/raw@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/quote@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/delimiter@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/table@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const initialData = @json($content);
                const editorData = (initialData && typeof initialData === 'object' && Array.isArray(initialData.blocks)) 
                    ? initialData 
                    : { blocks: [] };

                const editor = new EditorJS({
                    holder: 'editorjs',
                    placeholder: 'Click + to add text, headings, images, video embeds, or custom blocks...',
                    tools: {
                        header: {
                            class: window.Header,
                            inlineToolbar: true,
                            config: { levels: [1, 2, 3, 4, 5, 6], defaultLevel: 2 }
                        },
                        list: { 
                            class: window.NestedList || window.EditorjsList || window.List, 
                            inlineToolbar: true 
                        },
                        image: {
                            class: window.ImageTool || window.SimpleImage,
                            config: {
                                endpoints: { byFile: '{{ route('pages.upload-image') }}' },
                                additionalRequestHeaders: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                            }
                        },
                        embed: {
                            class: window.Embed,
                            inlineToolbar: true,
                            config: {
                                services: { youtube: true, vimeo: true, coub: true }
                            }
                        },
                        raw: {
                            class: window.RawTool,
                        },
                        quote: {
                            class: window.Quote,
                            inlineToolbar: true,
                        },
                        delimiter: {
                            class: window.Delimiter,
                        },
                        table: {
                            class: window.Table,
                            inlineToolbar: true,
                        }
                    },
                    data: editorData
                });

                document.getElementById('save-btn').addEventListener('click', () => {
                    const btn = document.getElementById('save-btn');
                    const originalText = btn.innerText;
                    btn.innerText = 'Saving...';
                    btn.disabled = true;

                    editor.save().then((outputData) => {
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
                            btn.innerText = 'Saved Successfully!';
                            btn.classList.replace('bg-uco-orange-500', 'bg-slate-900');
                            setTimeout(() => {
                                btn.innerText = originalText;
                                btn.classList.replace('bg-slate-900', 'bg-uco-orange-500');
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
            } catch (e) {
                console.error("EditorJS initialization failed:", e);
            }
        });
    </script>
    @endpush
</x-app-layout>
