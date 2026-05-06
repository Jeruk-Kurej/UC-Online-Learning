<x-app-layout>
    <div class="w-full max-w-[1600px] mx-auto">
        {{-- Page Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('contact-types.index') }}" 
                   class="group inline-flex items-center gap-2.5 px-4 py-2.5 bg-white hover:bg-gray-900 border border-gray-200 hover:border-gray-900 text-gray-700 hover:text-white rounded-xl font-medium text-sm shadow-sm hover:shadow-md transition-all duration-200">
                    <i class="bi bi-arrow-left text-base group-hover:-translate-x-0.5 transition-transform duration-200"></i>
                    <span>Back</span>
                </a>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-400 to-yellow-400 flex items-center justify-center text-white text-2xl">
                        <i class="{{ $contactType->icon_class }}"></i>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $contactType->platform_name }}</h1>
                        <p class="text-sm text-gray-600">Contact Type Details</p>
                    </div>
                </div>
            </div>

            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('contact-types.edit', $contactType) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white rounded-lg font-semibold text-sm shadow-sm transition duration-150">
                        <i class="bi bi-pencil me-2"></i>
                        Edit Contact Type
                    </a>
                @endif
            @endauth
        </div>

        {{-- Contact Type Information --}}
        <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-500">Platform Name</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $contactType->platform_name }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Icon Class</p>
                    <p class="mt-1">
                        <code class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-800">{{ $contactType->icon_class }}</code>
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Icon Preview</p>
                    <div class="mt-2 flex items-center gap-3">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-orange-400 to-yellow-400 flex items-center justify-center text-white text-2xl">
                            <i class="{{ $contactType->icon_class }}"></i>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-xl">
                            <i class="{{ $contactType->icon_class }}"></i>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-white border-2 border-gray-300 flex items-center justify-center text-gray-700 text-lg">
                            <i class="{{ $contactType->icon_class }}"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Contacts Using This Type</p>
                    <p class="mt-1">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $contactType->businessContacts->count() }} contacts
                        </span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Businesses Using This Contact Type --}}
        @if($contactType->businessContacts->count() > 0)
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    Contacts Using This Type ({{ $contactType->businessContacts->count() }})
                </h2>
                <div class="space-y-3">
                    @foreach($contactType->businessContacts as $contact)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-150">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <a href="{{ route('businesses.show', $contact->business) }}" class="text-sm font-semibold text-gray-900 hover:text-orange-600 transition">
                                            {{ $contact->business->name }}
                                        </a>
                                        @if($contact->is_primary)
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Primary
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <i class="{{ $contactType->icon_class }}"></i>
                                        <span class="font-mono">{{ $contact->contact_value }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="bi bi-person me-1"></i>
                                        Owner: {{ $contact->business->user->name }}
                                    </p>
                                </div>
                                <a href="{{ route('businesses.show', $contact->business) }}" 
                                   class="ml-4 text-orange-600 hover:text-orange-700 text-sm font-medium">
                                    View Business →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-white shadow-sm sm:rounded-lg p-12 text-center">
                <i class="bi bi-inbox text-6xl text-gray-300"></i>
                <p class="mt-4 text-gray-500 text-lg font-medium">No contacts using this type yet</p>
                <p class="mt-2 text-sm text-gray-600">This contact type will appear here once businesses start using it</p>
            </div>
        @endif
    </div>
</x-app-layout>