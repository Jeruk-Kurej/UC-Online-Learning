<x-app-layout>
    @push('styles')
        @vite('resources/css/vendor/tom-select.css')
    @endpush
    <x-offerings.form :business="$business" :type="$type" :offering="$offering" :photo-required="$photoRequired" />
</x-app-layout>
