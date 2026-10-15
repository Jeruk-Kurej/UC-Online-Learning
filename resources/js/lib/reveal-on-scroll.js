// --- Fade-in on scroll: adds .is-visible to each .reveal-on-scroll element the first time it enters the viewport ---
// Without IntersectionObserver everything is shown at once. Elements are marked with data-reveal-bound so they are
// observed only once.

let observer = null;

export default function revealOnScroll() {
    const targets = document.querySelectorAll('.reveal-on-scroll:not([data-reveal-bound="1"])');

    if (!targets.length) {
        return;
    }

    if (!('IntersectionObserver' in window)) {
        targets.forEach((target) => {
            target.classList.add('is-visible');
            target.dataset.revealBound = '1';
        });
        return;
    }

    if (!observer) {
        observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.02 },
        );
    }

    targets.forEach((target) => {
        target.dataset.revealBound = '1';
        observer.observe(target);
    });
}
