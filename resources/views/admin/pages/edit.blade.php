<x-app-layout>
    @section('title', 'Edit Page: ' . $page->title)

    <div class="py-12 px-6 max-w-[1200px] mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-black text-slate-900">Edit {{ $page->title }}</h1>
            <a href="{{ route($page->slug) }}" target="_blank" class="text-sm font-bold text-blue-600 hover:text-blue-800">
                View Public Page <i class="bi bi-box-arrow-up-right ml-1"></i>
            </a>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <p class="text-sm font-bold text-slate-500 mb-6 uppercase tracking-wider">Page Content Editor</p>
            <div id="editorjs" class="prose max-w-none min-h-[400px] p-6 border border-slate-100 rounded-xl bg-slate-50"></div>

            <div class="mt-8 flex justify-end">
                <button id="save-btn" class="px-6 py-3 bg-green-500 text-white font-bold rounded-xl hover:bg-green-600 transition-colors shadow-sm">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const editor = new EditorJS({
                    holder: 'editorjs',
                    placeholder: 'Start writing your content here...',
                    tools: {
                        header: {
                            class: window.Header,
                            inlineToolbar: true,
                            config: {
                                levels: [2, 3, 4],
                                defaultLevel: 2
                            }
                        },
                        list: {
                            class: window.NestedList || window.EditorjsList || window.List,
                            inlineToolbar: true,
                        },
                        image: {
                            class: window.ImageTool || window.SimpleImage,
                            config: {
                                endpoints: {
                                    byFile: '{{ route('pages.upload-image') }}',
                                },
                                additionalRequestHeaders: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            }
                        }
                    },
                    data: @json(is_string($page->content_json) ? json_decode($page->content_json) : ($page->content_json ?? new stdClass()))
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
                            btn.innerText = 'Saved!';
                            btn.classList.replace('bg-green-500', 'bg-slate-800');
                            setTimeout(() => {
                                btn.innerText = originalText;
                                btn.classList.replace('bg-slate-800', 'bg-green-500');
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
                alert("Failed to load editor. Error: " + e.message + " | Stack: " + e.stack);
            }
        });
    </script>
    @endpush
</x-app-layout>
