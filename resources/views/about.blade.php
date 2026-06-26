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
        {{-- Dynamic CMS Content --}}
        @if($page && $page->content_json && isset($page->content_json['blocks']))
        <section class="py-24 bg-white px-6 relative overflow-hidden">
            <div class="max-w-4xl mx-auto prose prose-lg prose-slate prose-headings:font-black">
                @foreach($page->content_json['blocks'] as $block)
                    @if($block['type'] === 'paragraph')
                        <p>{!! $block['data']['text'] !!}</p>
                    @elseif($block['type'] === 'header')
                        <h{{ $block['data']['level'] }}>{!! $block['data']['text'] !!}</h{{ $block['data']['level'] }}>
                    @elseif($block['type'] === 'list')
                        @if($block['data']['style'] === 'ordered')
                            <ol>
                                @foreach($block['data']['items'] as $item)
                                    <li>{!! $item !!}</li>
                                @endforeach
                            </ol>
                        @else
                            <ul>
                                @foreach($block['data']['items'] as $item)
                                    <li>{!! $item !!}</li>
                                @endforeach
                            </ul>
                        @endif
                    @elseif($block['type'] === 'image')
                        <figure>
                            <img src="{{ $block['data']['file']['url'] }}" alt="{{ $block['data']['caption'] ?? '' }}" class="rounded-2xl shadow-sm border border-slate-100">
                            @if(!empty($block['data']['caption']))
                                <figcaption class="text-center text-sm text-slate-500 mt-2">{!! $block['data']['caption'] !!}</figcaption>
                            @endif
                        </figure>
                    @endif
                @endforeach
            </div>
        </section>
        @endif

        {{-- Core Values --}}
        <section class="py-24 bg-white px-6 relative overflow-hidden">
            <div class="uco-ambient-glow uco-ambient-glow--purple uco-floating-blob-slow top-1/2 left-1/2"></div>

            {{-- Floating micro-shapes (Ada Kehidupan) --}}
            <div class="uco-floating-shape uco-floating-shape--plus top-[15%] left-[8%]"></div>
            <div class="uco-floating-shape uco-floating-shape--ring bottom-[20%] right-[10%]"></div>
            <div class="uco-floating-shape uco-floating-shape--dot top-[50%] left-[25%]"></div>

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
                <div class="relative inline-block mb-20">
                    {{-- Outline background text --}}
                    <div class="uco-outline-bg-text uco-outline-bg-text--orange uco-parallax-text left-1/2 -translate-x-1/2 -top-12">IMPACT</div>
                    <h2 class="text-4xl font-black text-white relative z-10 tracking-tight">Driving Community <span class="uco-text-gradient-orange">Impact</span></h2>
                </div>
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
            <div class="max-w-5xl mx-auto bg-slate-950 rounded-[3rem] p-12 md:p-20 text-center text-white relative overflow-hidden border border-slate-800/80 shadow-[0_24px_50px_-12px_rgba(0,0,0,0.5)] reveal-on-scroll">
                {{-- Grid Pattern --}}
                <div class="absolute inset-0 bg-[radial-gradient(#ffffff05_1px,transparent_1px)] [background-size:20px_20px] pointer-events-none opacity-60"></div>
                
                {{-- Glowing Ambient Mesh --}}
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[70%] h-[70%] bg-gradient-to-tr from-uco-orange-500/20 to-yellow-500/10 rounded-full filter blur-[100px] pointer-events-none opacity-80 z-0"></div>
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-blue-500/10 rounded-full filter blur-[80px] pointer-events-none z-0"></div>
                
                <h2 class="text-4xl md:text-5xl font-black mb-8 relative z-10 leading-tight">
                    Ready to build your <br class="sm:hidden">
                    <span class="bg-gradient-to-r from-uco-orange-400 via-amber-300 to-yellow-400 bg-clip-text text-transparent">legacy</span>?
                </h2>
                <p class="text-lg md:text-xl text-slate-400 mb-12 max-w-2xl mx-auto relative z-10 font-medium">
                    Join the UCO community today and gain access to a world of entrepreneurial opportunities.
                </p>
                <div class="flex flex-wrap justify-center gap-4 relative z-10">
                    <a href="{{ route('login') }}" class="uco-magnetic-btn px-10 py-5 bg-gradient-to-r from-uco-orange-500 to-amber-500 text-white font-black rounded-2xl shadow-[0_8px_30px_rgba(247,147,30,0.35)] transition-all duration-300 hover:scale-[1.05] hover:shadow-[0_12px_40px_rgba(247,147,30,0.5)]">
                        Get Started Now
                    </a>
                    <a href="{{ route('businesses.index') }}" class="uco-magnetic-btn px-10 py-5 bg-white/5 hover:bg-white/10 text-white font-bold rounded-2xl border border-white/10 hover:border-white/20 transition-all duration-300 hover:scale-[1.05] backdrop-blur-md shadow-lg">
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

                // Scroll-driven heading parallax for IMPACT text
                gsap.to('.uco-parallax-text', {
                    y: 40,
                    x: -20, // subtle horizontal shift too
                    scrollTrigger: {
                        trigger: '.uco-parallax-text',
                        start: "top bottom",
                        end: "bottom top",
                        scrub: 1
                    }
                });
            }

            // Spotlight hover coordinates tracker
            document.querySelectorAll('.uco-premium-card').forEach(card => {
                card.addEventListener('mousemove', e => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    card.style.setProperty('--mouse-x', `${x}px`);
                    card.style.setProperty('--mouse-y', `${y}px`);
                });
            });

            // Magnetic buttons elastic tracking
            if (typeof gsap !== 'undefined') {
                document.querySelectorAll('.uco-magnetic-btn').forEach(btn => {
                    btn.addEventListener('mousemove', e => {
                        const rect = btn.getBoundingClientRect();
                        const x = e.clientX - rect.left - (rect.width / 2);
                        const y = e.clientY - rect.top - (rect.height / 2);
                        
                        gsap.to(btn, {
                            x: x * 0.35,
                            y: y * 0.35,
                            duration: 0.3,
                            ease: "power2.out"
                        });
                    });
                    
                    btn.addEventListener('mouseleave', () => {
                        gsap.to(btn, {
                            x: 0,
                            y: 0,
                            duration: 0.65,
                            ease: "elastic.out(1.1, 0.4)"
                        });
                    });
                });

                // Mouse movements for background elements (parallax on mouse move)
                window.addEventListener('mousemove', e => {
                    const mouseX = (e.clientX / window.innerWidth - 0.5) * 20;
                    const mouseY = (e.clientY / window.innerHeight - 0.5) * 20;

                    gsap.to('.uco-floating-shape', {
                        x: -mouseX,
                        y: -mouseY,
                        duration: 1.5,
                        ease: "power1.out",
                        overwrite: "auto"
                    });
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
