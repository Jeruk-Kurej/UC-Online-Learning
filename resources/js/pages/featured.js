import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import testimonyCarousel from '../components/testimony-carousel';

// --- Featured page: testimony carousels (components/testimony-carousel) and GSAP scroll/hover animations ---
// Imported by app.js on pages with [data-featured-page]; Alpine starts after this module has run.
// window.gsap is read by the screenshot checks in .claude/safety-net to finish animations before a shot.

window.gsap = gsap;

function initFeaturedAnimations() {
    // --- GSAP animations ---
    gsap.registerPlugin(ScrollTrigger);

    // --- Sections 2-5 (intrapreneur students, entrepreneur students, ventures, testimonies) reveal their items ---
    const revealSections = [2, 3, 4, 5].map((n) => `section:nth-of-type(${n})`);

    // --- Default transitions are turned off only for animated items (avoids a flash of unstyled content) ---
    const animatedElements = document.querySelectorAll(
        revealSections.map((section) => `${section} .reveal-on-scroll`).join(', '),
    );
    animatedElements.forEach((el) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(40px)';
        el.style.transition = 'none';
    });

    revealSections.forEach((section) => {
        gsap.to(`${section} .reveal-on-scroll`, {
            opacity: 1,
            y: 0,
            duration: 0.6,
            stagger: 0.05,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: section,
                start: 'top 90%',
                toggleActions: 'play none none none',
            },
        });
    });

    // --- Scroll-driven heading parallax (alternating horizontal movement) ---
    gsap.utils.toArray('.uco-parallax-text').forEach((text, idx) => {
        const movementX = idx % 2 === 0 ? 100 : -100;
        gsap.to(text, {
            x: movementX,
            scrollTrigger: {
                trigger: text,
                start: 'top bottom',
                end: 'bottom top',
                scrub: 1.2,
            },
        });
    });

    // --- Spotlight hover coordinates ---
    document.querySelectorAll('.uco-premium-card').forEach((card) => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            card.style.setProperty('--mouse-x', `${x}px`);
            card.style.setProperty('--mouse-y', `${y}px`);
        });
    });

    // --- Magnetic buttons ---
    document.querySelectorAll('.uco-magnetic-btn').forEach((btn) => {
        btn.addEventListener('mousemove', (e) => {
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;

            gsap.to(btn, {
                x: x * 0.35,
                y: y * 0.35,
                duration: 0.3,
                ease: 'power2.out',
            });
        });

        btn.addEventListener('mouseleave', () => {
            gsap.to(btn, {
                x: 0,
                y: 0,
                duration: 0.65,
                ease: 'elastic.out(1.1, 0.4)',
            });
        });
    });

    // --- Background parallax on mouse move ---
    window.addEventListener('mousemove', (e) => {
        const mouseX = (e.clientX / window.innerWidth - 0.5) * 25;
        const mouseY = (e.clientY / window.innerHeight - 0.5) * 25;

        gsap.to('.uco-floating-shape', {
            x: -mouseX,
            y: -mouseY,
            duration: 1.5,
            ease: 'power1.out',
            overwrite: 'auto',
        });

        const orbs = document.querySelectorAll('.uco-float-orb');
        if (orbs.length >= 2) {
            gsap.to(orbs[0], {
                x: mouseX,
                y: mouseY,
                duration: 1.5,
                ease: 'power1.out',
                overwrite: 'auto',
            });
            gsap.to(orbs[1], {
                x: -mouseX,
                y: -mouseY,
                duration: 1.5,
                ease: 'power1.out',
                overwrite: 'auto',
            });
        }
    });
}

window.Alpine.data('testimonyCarousel', testimonyCarousel);

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFeaturedAnimations);
} else {
    initFeaturedAnimations();
}
