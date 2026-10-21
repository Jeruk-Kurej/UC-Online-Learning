{{-- --- Business page overview: description, facts and contact links --- --}}
<section class="card space-y-6" aria-labelledby="about-business">
    <div>
        <h2 id="about-business" class="card-title">About this business</h2>
        <p class="mt-2 whitespace-pre-line text-base text-gray-800">{{ $business->description }}</p>
    </div>

    <dl class="divide-y divide-line border-y border-line">
        @can('update', $business)
            <x-ui.fact label="Revenue"><i class="bi bi-lock" aria-hidden="true"></i> {{ $business->revenue_range ?? 'Not provided' }} <span class="text-sm text-gray-600">(not shown to visitors)</span></x-ui.fact>
        @endcan
        @if ($business->established_date)
            <x-ui.fact label="Founded">{{ $business->established_date->format('F Y') }}</x-ui.fact>
        @endif
        @if ($business->employee_count)
            <x-ui.fact label="Team size">{{ $business->employee_count }} people</x-ui.fact>
        @endif
        @if ($business->city || $business->province)
            <x-ui.fact label="Location">{{ $business->city ? $business->city . ($business->province ? ', ' . $business->province : '') : $business->province }}</x-ui.fact>
        @endif
        @if ($business->target_market)
            <x-ui.fact label="Target market">{{ $business->target_market }}</x-ui.fact>
        @endif
        @if ($business->customer_base_size)
            <x-ui.fact label="Customers">{{ $business->customer_base_size }}</x-ui.fact>
        @endif
        @if ($business->business_scale)
            <x-ui.fact label="Size of business">{{ $business->business_scale }}</x-ui.fact>
        @endif
        @if ($business->business_legality)
            <x-ui.fact label="Business permit">{{ $business->business_legality }}</x-ui.fact>
        @endif
        @if ($business->product_legality)
            <x-ui.fact label="Product permit">{{ $business->product_legality }}</x-ui.fact>
        @endif
        @if ($business->academic_heritage)
            <x-ui.fact label="Joined UC Online">{{ $business->academic_heritage }}</x-ui.fact>
        @endif
    </dl>

    @if ($business->website || $business->email || $business->instagram || $business->whatsapp)
        <div>
            <h3 class="mb-3 text-sm font-semibold text-gray-800">Contact</h3>
            <div class="space-y-2">
                @if ($business->website)
                    <x-people.contact-link :href="$business->website_url" icon="bi-globe" label="Website" new-tab>{{ $business->website_label }}</x-people.contact-link>
                @endif
                @if ($business->whatsapp)
                    <x-people.contact-link :href="$business->whatsapp_link" icon="bi-whatsapp" label="WhatsApp" new-tab>{{ $business->whatsapp }}</x-people.contact-link>
                @endif
                @foreach ($business->instagram_handles as $handle)
                    <x-people.contact-link :href="'https://instagram.com/' . $handle" icon="bi-instagram" label="Instagram" new-tab>{{ '@' . $handle }}</x-people.contact-link>
                @endforeach
                @if ($business->email)
                    <x-people.contact-link :href="'mailto:' . $business->email" icon="bi-envelope" label="Email">{{ $business->email }}</x-people.contact-link>
                @endif
            </div>
        </div>
    @elseif ($canManageBusiness)
        <div class="rounded-lg border border-dashed border-line p-4">
            <p class="text-base font-semibold text-gray-900">No contact details yet</p>
            <p class="mt-1 text-base text-gray-700">Customers cannot reach this business from its page. Add a WhatsApp number, an email, a website or an Instagram username.</p>
            <a href="{{ route('businesses.edit', $business) }}" class="text-link mt-2 inline-block">Add contact details</a>
        </div>
    @endif
</section>
