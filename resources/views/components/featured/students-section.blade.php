<x-ui.section :title="$look['title']" :lead="$look['lead']" :href="route('businesses.index', ['view' => $look['view']])" link-label="See the directory">
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($students as $student)
            <x-featured.student-card :student="$student" :type="$type" />
        @empty
            <x-ui.empty-state class="col-span-full" :title="$look['emptyTitle']">People picked by the admin team will show here.</x-ui.empty-state>
        @endforelse
    </div>
</x-ui.section>
