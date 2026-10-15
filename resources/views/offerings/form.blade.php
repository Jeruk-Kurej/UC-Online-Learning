<x-app-layout>
    @push('styles')
        @vite('resources/css/pages/offering-form.css')
    @endpush
    <x-offerings.form :business="$business" :type="$type" :offering="$offering" :photo-required="$photoRequired" />
</x-app-layout>
