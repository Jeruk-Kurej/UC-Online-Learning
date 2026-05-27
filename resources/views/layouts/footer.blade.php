<footer class="bg-white border-t border-gray-200 mt-16">
    {{-- ======================================== LAYOUT: FOOTER ======================================== --}}
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12">
            {{-- Column 1: About UCO (Span 4) --}}
            <div class="md:col-span-5 lg:col-span-4">
                <div class="flex items-center gap-2.5 mb-4">
                    <img src="{{ asset('images/logo-uco.png') }}" alt="UCO Logo" class="w-10 h-10 object-contain">
                    <div>
                        <h3 class="font-bold text-base text-gray-900">UC Online Learning</h3>
                        <p class="text-xs text-gray-600">Student & Alumni Community</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed max-w-sm">
                    Connecting students and alumni to build a stronger entrepreneurial community and foster business collaboration.
                </p>
            </div>

            {{-- Column 2: Quick Links (Span 3) --}}
            <div class="md:col-span-3 lg:col-span-3 lg:col-start-6">
                <h4 class="font-medium text-sm text-gray-900 mb-4">Quick Links</h4>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('featured') }}" class="text-sm text-gray-600 hover:text-uco-orange-500 transition-colors">
                            Featured
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('businesses.index') }}" class="text-sm text-gray-600 hover:text-uco-orange-500 transition-colors">
                            Showcase Directory
                        </a>
                    </li>
                    {{-- <li>
                        <a href="{{ route('uc-testimonies.index') }}" class="text-sm text-gray-600 hover:text-uco-orange-500 transition-colors">
                            Community Testimonies
                        </a>
                    </li> --}}
                    <li>
                        <a href="{{ route('about') }}" class="text-sm text-gray-600 hover:text-uco-orange-500 transition-colors">
                            About UCO
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Column 3: Contact (Span 4) --}}
            <div class="md:col-span-4 lg:col-span-4">
                <h4 class="font-medium text-sm text-gray-900 mb-4">Contact Us</h4>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-location-dot w-4 h-4 text-gray-900 flex-shrink-0 mt-0.5" aria-hidden="true"></i>
                        <a href="https://www.google.com/maps/search/?api=1&query=Universitas+Ciputra+Surabaya" target="_blank" class="leading-relaxed hover:text-uco-orange-500 transition-colors">UC Surabaya, CitraLand CBD Boulevard, Sambikerep, Surabaya</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-envelope w-4 h-4 text-gray-900" aria-hidden="true"></i>
                        <a href="mailto:pmb.online@ciputra.ac.id" class="hover:text-uco-orange-500 transition-colors">pmb.online@ciputra.ac.id</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-brands fa-whatsapp w-4 h-4 text-gray-900" aria-hidden="true"></i>
                        <a href="https://wa.me/6281216300303" target="_blank" class="hover:text-uco-orange-500 transition-colors">0812 16 300 303</a>
                    </li>
                </ul>

                {{-- Social Media Icons --}}
                <div class="mt-6">
                    <h5 class="font-medium text-sm text-gray-900 mb-3">Follow Us</h5>
                    <div class="flex gap-2.5">
                        <a href="https://www.facebook.com/uc.onlinelearning/" target="_blank" class="w-8 h-8 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center hover:bg-[#1877F2] hover:text-white transition-colors">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/uc_onlinelearning/?hl=en" target="_blank" class="w-8 h-8 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center hover:bg-gradient-to-tr hover:from-yellow-400 hover:via-red-500 hover:to-purple-500 hover:text-white transition-colors">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="https://www.tiktok.com/@uc_onlinelearning" target="_blank" class="w-8 h-8 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center hover:bg-black hover:text-white transition-colors">
                            <i class="fa-brands fa-tiktok"></i>
                        </a>
                        <a href="https://id.linkedin.com/company/universitas-ciputra-online" target="_blank" class="w-8 h-8 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center hover:bg-[#0A66C2] hover:text-white transition-colors">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Copyright - Center --}}
        <div class="mt-8 pt-6 border-t border-gray-200 text-center">
            <p class="text-sm text-gray-600">&copy; {{ date('Y') }} UCO Student & Alumni Platform. All rights reserved.</p>
        </div>
    </div>
</footer>