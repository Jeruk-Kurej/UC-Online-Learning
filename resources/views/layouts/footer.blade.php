<footer class="mt-16 border-t border-gray-200 bg-white">
    {{-- --- Footer --- --}}
    <div class="mx-auto max-w-[1600px] px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-12 lg:gap-12">
            {{-- --- Column 1: About UCO (Span 4) --- --}}
            <div class="md:col-span-5 lg:col-span-4">
                <div class="mb-4 flex items-center gap-2.5">
                    <img src="{{ asset('images/logo-uco.png') }}" alt="UCO Logo" class="h-10 w-10 object-contain">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">UC Online Learning</h3>
                        <p class="text-xs text-gray-600">Student & Alumni Community</p>
                    </div>
                </div>
                <p class="max-w-sm text-sm leading-relaxed text-gray-600">
                    Connecting students and alumni to build a stronger entrepreneurial community and foster business collaboration.
                </p>
            </div>

            {{-- --- Column 2: Quick Links (Span 3) --- --}}
            <div class="md:col-span-3 lg:col-span-3 lg:col-start-6">
                <h4 class="mb-4 text-sm font-medium text-gray-900">Quick Links</h4>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('featured') }}" class="text-sm text-gray-600 transition-colors hover:text-uco-orange-500">
                            Featured
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('businesses.index') }}" class="text-sm text-gray-600 transition-colors hover:text-uco-orange-500">
                            Business Directory</a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}" class="text-sm text-gray-600 transition-colors hover:text-uco-orange-500">
                            About UCO
                        </a>
                    </li>
                </ul>
            </div>

            {{-- --- Column 3: Contact (Span 4) --- --}}
            <div class="md:col-span-4 lg:col-span-4">
                <h4 class="mb-4 text-sm font-medium text-gray-900">Contact Us</h4>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-location-dot mt-0.5 h-4 w-4 flex-shrink-0 text-gray-900" aria-hidden="true"></i>
                        <a href="https://www.google.com/maps/search/?api=1&query=Universitas+Ciputra+Surabaya" target="_blank" class="leading-relaxed transition-colors hover:text-uco-orange-500">UC Surabaya, CitraLand CBD Boulevard, Sambikerep, Surabaya</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-envelope h-4 w-4 text-gray-900" aria-hidden="true"></i>
                        <a href="mailto:pmb.online@ciputra.ac.id" class="transition-colors hover:text-uco-orange-500">pmb.online@ciputra.ac.id</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-brands fa-whatsapp h-4 w-4 text-gray-900" aria-hidden="true"></i>
                        <a href="https://wa.me/6281216300303" target="_blank" class="transition-colors hover:text-uco-orange-500">0812 16 300 303</a>
                    </li>
                </ul>

                {{-- --- Social Media Icons --- --}}
                <div class="mt-6">
                    <h5 class="mb-3 text-sm font-medium text-gray-900">Follow Us</h5>
                    <div class="flex gap-2.5">
                        <a href="https://www.facebook.com/uc.onlinelearning/" target="_blank" class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-gray-600 transition-colors hover:bg-[#1877F2] hover:text-white">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/uc_onlinelearning/?hl=en" target="_blank" class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-gray-600 transition-colors hover:bg-gradient-to-tr hover:from-yellow-400 hover:via-red-500 hover:to-purple-500 hover:text-white">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="https://www.tiktok.com/@uc_onlinelearning" target="_blank" class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-gray-600 transition-colors hover:bg-black hover:text-white">
                            <i class="fa-brands fa-tiktok"></i>
                        </a>
                        <a href="https://id.linkedin.com/company/universitas-ciputra-online" target="_blank" class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-gray-600 transition-colors hover:bg-[#0A66C2] hover:text-white">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- --- Copyright - Center --- --}}
        <div class="mt-8 border-t border-gray-200 pt-6 text-center">
            <p class="text-sm text-gray-600">&copy; {{ date('Y') }} UCO Student & Alumni Platform. All rights reserved.</p>
        </div>
    </div>
</footer>
