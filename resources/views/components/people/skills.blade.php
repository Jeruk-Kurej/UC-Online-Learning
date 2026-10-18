@props(['skills'])

@if ($skills->isNotEmpty())
    <div class="border-t border-line pt-4">
        <h3 class="mb-2 text-sm font-semibold text-gray-800">Skills</h3>
        <ul class="flex flex-wrap gap-2">
            @foreach ($skills as $skill)
                <li class="badge badge-neutral">{{ $skill->name }}</li>
            @endforeach
        </ul>
    </div>
@endif
