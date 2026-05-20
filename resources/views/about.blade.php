<x-app-layout>
    @section('title', 'About Us')
    <div class="relative overflow-hidden">
        {{-- Hero Section --}}
        <section class="relative pt-24 pb-32 px-6 overflow-hidden">
            <div class="uco-hero-mesh"></div>
            {{-- Floating Ambient Glow Blobs --}}
            <div class="uco-ambient-glow uco-ambient-glow--orange uco-floating-blob-slow -top-20 -left-20"></div>
            <div class="uco-ambient-glow uco-ambient-glow--blue uco-floating-blob-slow bottom-0 right-0"></div>

            <div class="max-w-[1600px] mx-auto text-center relative z-10 reveal-on-scroll">
                <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-uco-orange-600 mb-8 animate-fade-in">
                    Our Vision
                </span>
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-black text-gray-900 tracking-tight mb-8">
                    Empowering the Next <br>
                    <span class="text-uco-orange-500">Generation of Founders.</span>
                </h1>
                <p class="max-w-3xl mx-auto text-lg md:text-xl text-gray-600 leading-relaxed font-medium">
                    The UCO platform is more than a directory. It's a high-performance ecosystem designed to accelerate the journey from student creator to industry leader.
                </p>
            </div>
        </section>

        {{-- Core Values --}}
        <section class="py-24 bg-white px-6 relative overflow-hidden">
            <div class="uco-ambient-glow uco-ambient-glow--purple uco-floating-blob-slow top-1/2 left-1/2"></div>

            <div class="max-w-[1600px] mx-auto relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div class="space-y-6 reveal-on-scroll uco-premium-card uco-premium-card--orange p-6 rounded-2xl border border-gray-100" style="transition-delay: 100ms;">
                        <div class="w-16 h-16 bg-uco-orange-50 text-uco-orange-500 rounded-lg flex items-center justify-center text-3xl shadow-sm border border-uco-orange-100">
                            <i class="bi bi-rocket-takeoff"></i>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900">Rapid Launch</h3>
                        <p class="text-gray-500 leading-relaxed font-medium">We provide the tools and network needed to transform academic theories into viable market products within weeks, not years.</p>
                    </div>
                    <div class="space-y-6 reveal-on-scroll uco-premium-card uco-premium-card--blue p-6 rounded-2xl border border-gray-100" style="transition-delay: 200ms;">
                        <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center text-3xl shadow-sm border border-blue-100">
                            <i class="bi bi-people"></i>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900">Global Network</h3>
                        <p class="text-gray-500 leading-relaxed font-medium">Connect with a diverse community of alumni mentors, industry experts, and fellow entrepreneurs across all major industries.</p>
                    </div>
                    <div class="space-y-6 reveal-on-scroll uco-premium-card uco-premium-card--orange p-6 rounded-2xl border border-gray-100" style="transition-delay: 300ms;">
                        <div class="w-16 h-16 bg-purple-50 text-purple-500 rounded-lg flex items-center justify-center text-3xl shadow-sm border border-purple-100">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900">Scalable Growth</h3>
                        <p class="text-gray-500 leading-relaxed font-medium">From local startups to multinational enterprises, our platform supports scaling businesses at every stage of their lifecycle.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Statistics / Impact --}}
        <section class="py-32 bg-gray-900 px-6 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 pointer-events-none">
                <div class="w-full h-full bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:40px_40px]"></div>
            </div>
            <div class="max-w-[1600px] mx-auto relative z-10 text-center reveal-on-scroll">
                <h2 class="text-4xl font-black text-white mb-20 tracking-tight">Driving Community Impact</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-12">
                    <div class="space-y-2">
                        <p class="text-5xl font-black text-uco-orange-500 tracking-tighter"><span class="stat-number" data-target="500">500</span>+</p>
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Active Ventures</p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-5xl font-black text-white tracking-tighter"><span class="stat-number" data-target="1200">1200</span>+</p>
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Graduated Founders</p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-5xl font-black text-white tracking-tighter"><span class="stat-number" data-target="24">24</span></p>
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Industry Categories</p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-5xl font-black text-white tracking-tighter"><span class="stat-number" data-target="15">15</span>+</p>
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Years of Heritage</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- CTA Section --}}
        <section class="py-32 px-6">
            <div class="max-w-5xl mx-auto bg-uco-orange-500 rounded-[3rem] p-12 md:p-20 text-center text-white relative overflow-hidden shadow-2xl reveal-on-scroll">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-32 -mt-32"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-black/10 rounded-full blur-3xl -ml-32 -mb-32"></div>
                
                <h2 class="text-4xl md:text-5xl font-black mb-8 relative z-10">Ready to build your legacy?</h2>
                <p class="text-lg md:text-xl text-white/90 mb-12 max-w-2xl mx-auto relative z-10">
                    Join the UCO community today and gain access to a world of entrepreneurial opportunities.
                </p>
                <div class="flex flex-wrap justify-center gap-4 relative z-10">
                    <a href="{{ route('login') }}" class="px-10 py-5 bg-white text-uco-orange-500 font-black rounded-lg hover:bg-gray-50 transition shadow-xl hover:-translate-y-1">
                        Get Started Now
                    </a>
                    <a href="{{ route('businesses.index') }}" class="px-10 py-5 bg-uco-orange-600 text-white font-black rounded-lg hover:bg-uco-orange-700 transition shadow-xl">
                        Explore Directory
                    </a>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
                gsap.registerPlugin(ScrollTrigger);

                // Disable default transitions during entry scroll to avoid conflict with GSAP
                document.querySelectorAll('.reveal-on-scroll').forEach(el => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(40px)';
                    el.style.transition = 'none';
                });

                // Animate elements in the Hero & Core Values sections
                gsap.to(".reveal-on-scroll", {
                    opacity: 1,
                    y: 0,
                    duration: 0.9,
                    ease: "power3.out",
                    stagger: 0.15,
                    scrollTrigger: {
                        trigger: ".relative.pt-24",
                        start: "top 80%"
                    }
                });

                // Count up animation for stats
                gsap.utils.toArray('.stat-number').forEach(stat => {
                    const target = parseInt(stat.getAttribute('data-target'));
                    stat.innerText = '0';
                    gsap.to(stat, {
                        innerText: target,
                        duration: 1.8,
                        snap: { innerText: 1 },
                        ease: "power2.out",
                        scrollTrigger: {
                            trigger: stat,
                            start: "top 90%",
                            toggleActions: "play none none none"
                        }
                    });
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
