<footer class="bg-white border-t border-gray-200 mt-16">
    {{-- ======================================== LAYOUT: FOOTER ======================================== --}}
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {{-- Column 1: About UCO --}}
            <div>
                <div class="flex items-center gap-2.5 mb-4">
                    <img src="{{ asset('images/Logo UCO.png') }}" alt="UCO Logo" class="w-10 h-10 object-contain">
                    <div>
                        <h3 class="font-bold text-base text-gray-900">UC Online Learning</h3>
                        <p class="text-xs text-gray-600">Student & Alumni Community</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Connecting students and alumni to build a stronger entrepreneurial community and foster business collaboration.
                </p>
            </div>

            {{-- Column 2: Quick Links --}}
            <div>
                <h4 class="font-medium text-sm text-gray-900 mb-4">Quick Links</h4>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('featured') }}" class="text-sm text-gray-600 hover:text-uco-orange-500 transition-colors">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('businesses.index') }}" class="text-sm text-gray-600 hover:text-uco-orange-500 transition-colors">
                            Business Directory
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('uc-testimonies.index') }}" class="text-sm text-gray-600 hover:text-uco-orange-500 transition-colors">
                            Community Testimonies
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}" class="text-sm text-gray-600 hover:text-uco-orange-500 transition-colors">
                            About UCO
                        </a>
                    </li>
                    @auth
                        <li>
                            <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 hover:text-uco-orange-500 transition-colors">
                                My Profile
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>

            <!-- {{-- Column 3: Resources --}}
            <div>
                <h4 class="font-medium text-sm text-gray-900 mb-4">Resources</h4>
                <ul class="space-y-2">
                    <li>
                        <a href="#" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                            User Guide
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                            FAQ
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                            Privacy Policy
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                            Terms of Service
                        </a>
                    </li>
                </ul>
            </div> -->

            {{-- Column 4: Contact --}}
            <div>
                <h4 class="font-medium text-sm text-gray-900 mb-4">Contact Us</h4>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-location-dot w-4 h-4 text-gray-900 flex-shrink-0 mt-0.5"></i>
                        <span>UC Surabaya, CitraLand CBD Boulevard, Sambikerep, Surabaya</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-envelope w-4 h-4 text-gray-900"></i>
                        <a href="mailto:info@uco.ac.id" class="hover:text-gray-900 transition-colors">info@uco.ac.id</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-phone w-4 h-4 text-gray-900"></i>
                        <a href="tel:+62317000000" class="hover:text-gray-900 transition-colors">+62 (31) 700-0000</a>
                    </li>
                </ul>

                {{-- Social Media Icons --}}
                <div class="mt-4">
                    <h5 class="font-medium text-sm text-gray-900 mb-3">Follow Us</h5>
                    <div class="flex gap-2">
                        <a href="#" class="w-8 h-8 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center hover:bg-gray-900 hover:text-white transition-colors">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center hover:bg-gray-900 hover:text-white transition-colors">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center hover:bg-gray-900 hover:text-white transition-colors">
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