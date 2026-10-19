{{-- --- Profile certificates: images open in the page viewer, PDFs open in a new tab --- --}}
@if (count($user->certification_files) > 0)
    <section class="card">
        <h2 class="card-title">Certificates</h2>
        <ul class="mt-4 grid grid-cols-2 gap-3">
            @foreach ($user->certification_files as $cert)
                <li>
                    @if ($cert['isPdf'])
                        <a href="{{ $cert['originalUrl'] }}" target="_blank" rel="noopener" class="flex aspect-[4/3] flex-col items-center justify-center gap-1 rounded-md border border-line bg-gray-50 text-center text-sm text-gray-800 hover:border-brand-500">
                            <i class="bi bi-file-earmark-pdf text-3xl text-red-700" aria-hidden="true"></i>
                            Open PDF
                        </a>
                    @else
                        <button type="button" @click="open(@js($cert['previewUrl']))" class="block aspect-[4/3] w-full overflow-hidden rounded-md border border-line bg-gray-50 hover:border-brand-500" aria-label="View certificate larger">
                            <img src="{{ $cert['previewUrl'] }}" alt="" loading="lazy" class="h-full w-full object-contain">
                        </button>
                    @endif
                </li>
            @endforeach
        </ul>
    </section>
@endif
