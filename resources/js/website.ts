import '../css/app.css';

// Alpine is now automatically injected by Livewire v3.
// Do not import and start it manually to avoid "multiple instances" conflict.

// ═══════════════════ REVEAL ON SCROLL + COUNTER ═══════════════════

type CountTarget = HTMLElement & { _counterDone?: boolean };

function animateCounter(el: HTMLElement): void {
    const raw = el.dataset.counter || el.textContent || '0';
    const match = raw.match(/([\d.,]+)(.*)/);
    if (!match) return;
    const numStr = match[1].replace(/\./g, '').replace(/,/g, '');
    const target = parseInt(numStr, 10);
    if (isNaN(target) || target === 0) return;
    const suffix = match[2] ?? '';
    const duration = 1600;
    const start = performance.now();

    function tick(now: number): void {
        const p = Math.min((now - start) / duration, 1);
        const eased = p === 1 ? 1 : 1 - Math.pow(2, -10 * p);
        const current = Math.round(target * eased);
        el.textContent = current.toLocaleString('id-ID') + suffix;
        if (p < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
}

function init(): void {
    const revealEls = document.querySelectorAll<HTMLElement>('[data-reveal]');
    const counterEls = document.querySelectorAll<CountTarget>('[data-counter]');

    const revealObs = new IntersectionObserver((entries, obs) => {
        for (const entry of entries) {
            if (entry.isIntersecting) {
                const el = entry.target as HTMLElement;
                const delay = el.dataset.revealDelay ?? '0';
                el.style.transitionDelay = `${delay}ms`;
                el.classList.add('reveal-visible');
                obs.unobserve(el);
            }
        }
    }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });

    revealEls.forEach((el) => revealObs.observe(el));

    const counterObs = new IntersectionObserver((entries, obs) => {
        for (const entry of entries) {
            if (entry.isIntersecting) {
                const el = entry.target as CountTarget;
                if (!el._counterDone) {
                    el._counterDone = true;
                    animateCounter(el);
                }
                obs.unobserve(el);
            }
        }
    }, { threshold: 0.4 });

    counterEls.forEach((el) => counterObs.observe(el));
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

// ═══════════════════ TESTIMONIAL SLIDER (Alpine) ═══════════════════
// Responsive: mobile=1, tablet=2, desktop=3, xl=4
// Auto-slide 4s, pause on hover, prev/next arrows

function getCardsPerView(): number {
    const w = window.innerWidth;
    if (w >= 1280) return 4; // xl
    if (w >= 1024) return 3; // lg
    if (w >= 768) return 2;  // md
    return 1;
}

document.addEventListener('alpine:init', () => {
    window.Alpine.data('testimonialSlider', () => ({
        index: 0,
        cardsPerView: getCardsPerView(),
        gap: 24,
        intervalId: null as ReturnType<typeof setInterval> | null,

        get offset() {
            const card = (this.$refs.track as HTMLElement).querySelector<HTMLElement>(':scope > *');
            if (!card) return 0;
            return this.index * (card.offsetWidth + this.gap);
        },

        get maxIndex() {
            const track = this.$refs.track as HTMLElement;
            const cards = track.querySelectorAll<HTMLElement>(':scope > *');
            return Math.max(0, cards.length - this.cardsPerView);
        },

        next() {
            this.index = this.index >= this.maxIndex ? 0 : this.index + 1;
        },

        prev() {
            this.index = this.index <= 0 ? this.maxIndex : this.index - 1;
        },

        startAuto() {
            this.stopAuto();
            this.intervalId = setInterval(() => this.next(), 4000);
        },

        stopAuto() {
            if (this.intervalId) { clearInterval(this.intervalId); this.intervalId = null; }
        },

        onResize() {
            this.cardsPerView = getCardsPerView();
            if (this.index > this.maxIndex) this.index = this.maxIndex;
        },

        init() {
            this.startAuto();
            window.addEventListener('resize', () => this.onResize());
        },

        destroy() {
            this.stopAuto();
        },
    }));
});
