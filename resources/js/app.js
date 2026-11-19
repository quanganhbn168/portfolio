import Collapse from 'bootstrap/js/dist/collapse';
import Dropdown from 'bootstrap/js/dist/dropdown';
import Modal from 'bootstrap/js/dist/modal';
import Offcanvas from 'bootstrap/js/dist/offcanvas';
import Tab from 'bootstrap/js/dist/tab';


window.bootstrap = { Collapse, Dropdown, Modal, Offcanvas, Tab };

function initialiseHeader() {
    const header = document.querySelector('[data-site-header]');
    const drawer = document.getElementById('mobile-drawer');
    const search = document.getElementById('header-search-modal');

    search?.addEventListener('shown.bs.modal', () => document.getElementById('header-search-query')?.focus());
    matchMedia('(min-width: 1200px)').addEventListener('change', ({ matches }) => {
        if (matches && drawer) Offcanvas.getInstance(drawer)?.hide();
    });

    if (!header) return;

    let previousScroll = Math.max(0, window.scrollY);

    window.addEventListener('scroll', () => {
        const currentScroll = Math.max(0, window.scrollY);

        if (currentScroll <= header.offsetHeight) {
            header.classList.remove('is-scroll-hidden');
        } else if (Math.abs(currentScroll - previousScroll) < 5) {
            return;
        } else {
            header.classList.toggle('is-scroll-hidden', currentScroll > previousScroll);
        }

        previousScroll = currentScroll;
    }, { passive: true });
}

function initialiseScrollTop() {
    const button = document.querySelector('[data-scroll-top]');
    if (!button) return;

    const toggle = () => button.classList.toggle('is-visible', window.scrollY > 480);
    toggle();
    window.addEventListener('scroll', toggle, { passive: true });
    button.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

function initialiseGalleries() {
    document.querySelectorAll('[data-gallery]').forEach((gallery) => {
        const main = gallery.querySelector('[data-gallery-main]');
        const thumbs = gallery.querySelectorAll('[data-gallery-thumb]');
        if (!main || !thumbs.length) return;

        thumbs.forEach((thumb) => {
            thumb.addEventListener('click', () => {
                main.src = thumb.dataset.src;
                thumbs.forEach((item) => {
                    const isActive = item === thumb;
                    item.classList.toggle('is-active', isActive);
                    item.setAttribute('aria-pressed', String(isActive));
                });
            });
        });
    });
}

function initialiseAos() {
    const elements = document.querySelectorAll('[data-aos]');
    if (!elements.length) return;

    // Người dùng tắt chuyển động: bỏ thuộc tính để nội dung hiện luôn, không tải thư viện.
    if (matchMedia('(prefers-reduced-motion: reduce)').matches) {
        elements.forEach((element) => element.removeAttribute('data-aos'));
        return;
    }

    void import('aos').then(({ default: AOS }) => {
        AOS.init({ duration: 700, easing: 'ease-out-cubic', once: true, offset: 60 });
    });
}

function initialiseCountUp() {
    const groups = document.querySelectorAll('[data-count-up]');
    if (!groups.length || matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) return;

    const run = (group) => {
        group.querySelectorAll('[data-count-target]').forEach((element) => {
            const target = Number.parseInt(element.dataset.countTarget, 10);
            if (!Number.isFinite(target)) return;

            const start = performance.now();
            const duration = 1400;
            const tick = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                element.textContent = String(Math.round(target * (1 - (1 - progress) ** 3)));
                if (progress < 1) requestAnimationFrame(tick);
            };

            element.textContent = '0';
            requestAnimationFrame(tick);
        });
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            observer.unobserve(entry.target);
            run(entry.target);
        });
    }, { threshold: 0.4 });

    groups.forEach((group) => observer.observe(group));
}

function initialise() {
    initialiseAos();
    initialiseCountUp();
    initialiseGalleries();
    initialiseHeader();
    initialiseScrollTop();
    if (document.querySelector('[data-hero-swiper], [data-about-swiper], [data-testimonial-swiper], [data-project-gallery], [data-project-site-swiper], [data-project-related-swiper]')) {
        void import('./frontend/sliders').then((module) => module.initialiseSliders());
    }

    if (document.querySelector('.fa-solid, .fa-regular, .fa-brands, .fas, .far, .fab, .fa')) {
        void import('./frontend/icons');
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialise, { once: true });
} else {
    initialise();
}
