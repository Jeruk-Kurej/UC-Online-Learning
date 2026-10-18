{{-- --- Top of a business or work-profile page: logo, badges, name and the actions slot --- --}}
@props(['name', 'logoUrl'])

<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-4 sm:gap-5">
        <x-ui.thumb :src="$logoUrl" :name="$name" size="h-20 w-20" class="text-3xl" />
        <div class="min-w-0">
            <div class="mb-2 flex flex-wrap items-center gap-2">
                {{ $badges }}
            </div>
            <h1 class="page-title break-words">{{ $name }}</h1>
        </div>
    </div>

    {{ $actions }}
</div>
