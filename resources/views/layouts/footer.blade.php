<footer class="mt-16 bg-ink text-white">
    <div @class([
        'mx-auto px-4 py-12 sm:px-6 lg:px-8',
        'max-w-6xl' => !$wide,
        'max-w-[96rem]' => $wide,
    ])>
        <div class="grid grid-cols-1 gap-10 md:grid-cols-3">
            {{-- --- Who we are --- --}}
            <div>
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-uco-128.webp') }}" alt="" class="h-12 w-12 object-contain">
                    <div>
                        <p class="font-display text-xl font-semibold">UC Online Learning</p>
                        <p class="text-sm text-gray-300">Student and alumni community</p>
                    </div>
                </div>
                <p class="mt-4 max-w-sm text-base text-gray-200">
                    A directory of the businesses and careers of Universitas Ciputra Online Learning students and alumni.
                </p>
            </div>

            {{-- --- Pages --- --}}
            <nav aria-label="Footer">
                <p class="text-base font-semibold text-brand-300">Pages</p>
                <ul class="mt-3 space-y-2 text-base">
                    <li><a href="{{ route('home') }}" class="text-gray-100 underline-offset-2 hover:underline">Home</a></li>
                    <li><a href="{{ route('businesses.index') }}" class="text-gray-100 underline-offset-2 hover:underline">Directory</a></li>
                    <li><a href="{{ route('about') }}" class="text-gray-100 underline-offset-2 hover:underline">About</a></li>
                </ul>
            </nav>

            {{-- --- Contact --- --}}
            <div>
                <p class="text-base font-semibold text-brand-300">Contact</p>
                <ul class="mt-3 space-y-3 text-base text-gray-100">
                    <li class="flex items-start gap-3">
                        <i class="bi bi-geo-alt mt-0.5" aria-hidden="true"></i>
                        <a href="https://www.google.com/maps/search/?api=1&query=Universitas+Ciputra+Surabaya" target="_blank" rel="noopener" class="underline-offset-2 hover:underline">UC Surabaya, CitraLand CBD Boulevard, Sambikerep, Surabaya</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="bi bi-envelope" aria-hidden="true"></i>
                        <a href="mailto:pmb.online@ciputra.ac.id" class="underline-offset-2 hover:underline">pmb.online@ciputra.ac.id</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="bi bi-whatsapp" aria-hidden="true"></i>
                        <a href="https://wa.me/6281216300303" target="_blank" rel="noopener" class="underline-offset-2 hover:underline">0812 16 300 303</a>
                    </li>
                </ul>

                <div class="mt-5 flex gap-2">
                    <a href="https://www.facebook.com/uc.onlinelearning/" target="_blank" rel="noopener" class="flex h-10 w-10 items-center justify-center rounded-md bg-white/10 text-lg hover:bg-white/20" aria-label="Facebook"><i class="bi bi-facebook" aria-hidden="true"></i></a>
                    <a href="https://www.instagram.com/uc_onlinelearning/?hl=en" target="_blank" rel="noopener" class="flex h-10 w-10 items-center justify-center rounded-md bg-white/10 text-lg hover:bg-white/20" aria-label="Instagram"><i class="bi bi-instagram" aria-hidden="true"></i></a>
                    <a href="https://www.tiktok.com/@uc_onlinelearning" target="_blank" rel="noopener" class="flex h-10 w-10 items-center justify-center rounded-md bg-white/10 text-lg hover:bg-white/20" aria-label="TikTok"><i class="bi bi-tiktok" aria-hidden="true"></i></a>
                    <a href="https://id.linkedin.com/company/universitas-ciputra-online" target="_blank" rel="noopener" class="flex h-10 w-10 items-center justify-center rounded-md bg-white/10 text-lg hover:bg-white/20" aria-label="LinkedIn"><i class="bi bi-linkedin" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>

        <p class="mt-10 border-t border-white/20 pt-6 text-sm text-gray-300">&copy; {{ date('Y') }} Universitas Ciputra Online Learning</p>
    </div>
</footer>
