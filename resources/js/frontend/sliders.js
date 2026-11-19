import Swiper from 'swiper';
import { A11y, Autoplay, EffectFade, Keyboard, Navigation, Pagination, Thumbs } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/effect-fade';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/thumbs';

// Video nền của banner chỉ phát khi người dùng thật sự xem được: không bật "giảm chuyển động", không ở chế độ
// tiết kiệm dữ liệu / mạng 2G. Màn hình nhỏ chỉ hiện ảnh bìa (đỡ tốn dữ liệu), trừ slide bật "chạy trên điện thoại".
const canPlayHeroVideo = () => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return false;
    const connection = navigator.connection;
    if (connection && (connection.saveData || /(^|-)2g$/.test(connection.effectiveType || ''))) return false;
    return true;
};

const initialiseHeroVideos = (element, swiper) => {
    const videos = element.querySelectorAll('video[data-hero-video]');
    const frames = element.querySelectorAll('iframe[data-hero-youtube]');
    if (!videos.length && !frames.length) return;

    const hero = element.closest('[data-hero-section]') ?? element;
    let heroVisible = true;
    let ready = false;

    const smallScreen = window.matchMedia('(max-width: 767.98px)');
    const allowed = (media) => ready && (!smallScreen.matches || media?.hasAttribute('data-play-mobile'));

    const activeVideo = () => swiper.slides[swiper.activeIndex]?.querySelector('video[data-hero-video]') ?? null;

    // Nhiều slide: slide video chạy hết một lần rồi mới chuyển (bộ đếm giờ tạm dừng); một slide: video lặp.
    const multiple = swiper.slides.length > 1;
    const advance = () => {
        swiper.autoplay.start();
        swiper.slideNext();
    };

    if (multiple) {
        videos.forEach((video) => {
            video.loop = false;
            video.addEventListener('ended', advance);
            video.addEventListener('error', () => { if (video === activeVideo()) advance(); });
        });
    }

    const activeFrame = () => swiper.slides[swiper.activeIndex]?.querySelector('iframe[data-hero-youtube]') ?? null;

    const sync = () => {
        const current = activeVideo();
        const currentFrame = activeFrame();
        // YouTube nền: chỉ nạp khi slide đang hiện, gỡ đi khi rời slide (dừng tải và dừng phát).
        frames.forEach((frame) => {
            if (frame === currentFrame && allowed(frame) && heroVisible && !document.hidden) {
                if (!frame.getAttribute('src')) {
                    frame.addEventListener('load', () => window.setTimeout(() => frame.classList.add('is-playing'), 900), { once: true });
                    frame.src = frame.dataset.src;
                }
            } else if (frame.getAttribute('src')) {
                frame.removeAttribute('src');
                frame.classList.remove('is-playing');
            }
        });
        if (multiple && ready) {
            if (current && allowed(current)) {
                if (swiper.autoplay.running) swiper.autoplay.stop();
            } else if (!swiper.autoplay.running) {
                swiper.autoplay.start();
            }
        }
        videos.forEach((video) => {
            if (video !== current || !allowed(video) || !heroVisible || document.hidden) {
                if (!video.paused) video.pause();
                return;
            }
            // Chỉ nạp file video của slide đang hiển thị (swiper loop nhân bản slide, không tải trùng).
            if (!video.getAttribute('src')) {
                video.src = video.dataset.src;
                video.addEventListener('playing', () => video.classList.add('is-playing'), { once: true });
            }
            if (video.ended) video.currentTime = 0;
            video.play().catch(() => {});
        });
    };

    new IntersectionObserver((entries) => {
        heroVisible = entries[0]?.isIntersecting ?? true;
        sync();
    }, { threshold: 0.15 }).observe(hero);

    document.addEventListener('visibilitychange', sync);
    swiper.on('slideChange', sync);
    smallScreen.addEventListener('change', sync);

    // Chờ trang tải xong và trình duyệt rảnh mới bắt đầu tải video, để không tranh băng thông với ảnh và chữ.
    const start = () => {
        if (!canPlayHeroVideo()) return;
        const run = () => { ready = true; sync(); };
        'requestIdleCallback' in window ? window.requestIdleCallback(run, { timeout: 2500 }) : window.setTimeout(run, 800);
    };
    document.readyState === 'complete' ? start() : window.addEventListener('load', start, { once: true });
};

// Nút Play YouTube: phát ngay trong cửa sổ nổi (Bootstrap Modal); không có JS thì link mở YouTube ở tab mới.
const initialiseHeroYoutube = (element, swiper) => {
    const modalElement = document.getElementById('hero-video-modal');
    const Modal = window.bootstrap?.Modal;
    if (!modalElement || !Modal) return;

    const frame = modalElement.querySelector('iframe');
    const modal = Modal.getOrCreateInstance(modalElement);

    element.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-hero-video-embed]');
        if (!trigger) return;

        event.preventDefault();
        frame.src = trigger.dataset.heroVideoEmbed;
        swiper.autoplay.stop();
        modal.show();
    });

    modalElement.addEventListener('hidden.bs.modal', () => {
        frame.src = '';
        if (swiper.slides.length > 1) swiper.autoplay.start();
        swiper.emit('slideChange');
    });
};

const initialiseHeroSwipers = () => {
    document.querySelectorAll('[data-hero-swiper]').forEach((element) => {
        if (element.swiper) return;

        const swiper = new Swiper(element, {
            modules: [A11y, Autoplay, EffectFade, Keyboard, Pagination],
            effect: 'fade',
            fadeEffect: { crossFade: true },
            autoHeight: true,
            speed: 700,
            loop: element.querySelectorAll('.swiper-slide').length > 1,
            autoplay: {
                delay: 7000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            keyboard: { enabled: true },
            pagination: {
                el: element.parentElement?.querySelector('[data-hero-pagination]'),
                clickable: true,
            },
        });

        initialiseHeroVideos(element, swiper);
        initialiseHeroYoutube(element, swiper);
    });
};

const initialiseTestimonialSwipers = () => {
    document.querySelectorAll('[data-testimonial-swiper]').forEach((element) => {
        if (element.swiper) return;
        const section = element.closest('.testimonials') ?? element.parentElement;

        new Swiper(element, {
            modules: [A11y, Autoplay, Keyboard, Navigation, Pagination],
            slidesPerView: 1,
            spaceBetween: 16,
            rewind: true,
            autoplay: {
                delay: 6500,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            keyboard: { enabled: true },
            watchOverflow: true,
            navigation: {
                prevEl: section?.querySelector('[data-testimonial-swiper-prev]'),
                nextEl: section?.querySelector('[data-testimonial-swiper-next]'),
            },
            pagination: {
                el: section?.querySelector('[data-testimonial-swiper-pagination]'),
                clickable: true,
            },
            breakpoints: {
                768: { slidesPerView: 2, spaceBetween: 20 },
                1200: { slidesPerView: 3, spaceBetween: 24 },
            },
        });
    });
};


const initialiseAboutSwipers = () => {
    document.querySelectorAll('[data-about-swiper]').forEach((element) => {
        if (element.swiper) return;
        const section = element.parentElement;

        new Swiper(element, {
            modules: [A11y, Keyboard, Navigation],
            slidesPerView: 1.1,
            spaceBetween: 16,
            speed: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 450,
            keyboard: { enabled: true },
            watchOverflow: true,
            navigation: {
                prevEl: section.querySelector('[data-about-swiper-prev]'),
                nextEl: section.querySelector('[data-about-swiper-next]'),
            },
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 20 },
            },
        });
    });
};

export function initialiseSliders() {
    initialiseAboutSwipers();
    initialiseProjectSwipers();
    initialiseHeroSwipers();
    initialiseTestimonialSwipers();
}

function initialiseProjectSwipers() {
    const a11y = { prevSlideMessage: 'Ảnh trước', nextSlideMessage: 'Ảnh tiếp theo', paginationBulletMessage: 'Đến nhóm ảnh {{index}}' };
    const speed = matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 400;
    document.querySelectorAll('[data-project-gallery]').forEach((element) => {
        if (element.swiper) return;
        const shell = element.closest('.project-case__gallery');
        const thumbElement = shell.querySelector('[data-project-thumbs]');
        const thumbs = new Swiper(thumbElement, {
            modules: [A11y], slidesPerView: 3, spaceBetween: 8, watchSlidesProgress: true,
            breakpoints: { 576: { slidesPerView: 5 } },
        });
        const gallery = new Swiper(element, {
            modules: [A11y, Navigation, Keyboard, Thumbs], a11y, speed,
            keyboard: { enabled: true }, watchOverflow: true,
            thumbs: { swiper: thumbs },
            navigation: { prevEl: shell.querySelector('[data-project-prev]'), nextEl: shell.querySelector('[data-project-next]') },
        });
        const buttons = [...thumbElement.querySelectorAll('button')];
        const update = () => buttons.forEach((button, index) => button.setAttribute('aria-pressed', String(index === gallery.activeIndex)));
        buttons.forEach((button, index) => button.addEventListener('click', () => gallery.slideTo(index)));
        gallery.on('slideChange', update);
        update();
    });
    document.querySelectorAll('[data-project-site-swiper], [data-project-related-swiper]').forEach((element) => {
        if (element.swiper) return;
        const shell = element.closest('.project-case__slider-shell');
        new Swiper(element, {
            modules: [A11y, Navigation, Keyboard, Pagination], a11y, speed,
            slidesPerView: 1.2, spaceBetween: 16, keyboard: { enabled: true }, watchOverflow: true,
            navigation: { prevEl: shell.querySelector('[data-strip-prev]'), nextEl: shell.querySelector('[data-strip-next]') },
            pagination: { el: element.querySelector('.swiper-pagination'), clickable: true },
            breakpoints: { 576: { slidesPerView: 2 }, 992: { slidesPerView: 3 }, 1200: { slidesPerView: 4 } },
        });
    });
}
