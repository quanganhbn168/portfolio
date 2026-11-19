{{-- Trang Portfolio: hero + số liệu, tab lọc theo lĩnh vực, lưới dự án, case study, CTA. --}}
@php
    $hotline = $website->primaryPhone() ?: $website->hotline;
@endphp

@if (filled($website->owner_name) || filled($website->owner_bio))
    @php
        $ownerImage = \App\Support\Media\MediaUrl::webp($websiteMedia->get($website->owner_media_id), 480);
        $coverImage = \App\Support\Media\MediaUrl::webp($websiteMedia->get($website->owner_cover_media_id), 1600);
        $contactEmail = $website->contact_email;
    @endphp
    <section class="section-space pb-0" id="toi-la-ai">
        <div class="container">
            <article class="fb-profile">
                <div class="fb-profile__cover">
                    @if ($coverImage)
                        <img src="{{ $coverImage }}" alt="" fetchpriority="high">
                    @endif
                </div>

                <div class="fb-profile__head">
                    @if ($ownerImage)
                        <img class="fb-profile__avatar" src="{{ $ownerImage }}" alt="{{ $website->owner_name }}" width="176" height="176">
                    @endif
                    <div class="fb-profile__id">
                        <h2>{{ $website->owner_name }}</h2>
                        @if ($website->owner_title)<p class="fb-profile__role">{{ $website->owner_title }}</p>@endif
                        <p class="fb-profile__counts">
                            <span><strong>{{ $portfolioProjects->count() }}</strong> {{ __('dự án') }}</span>
                            <span><strong>{{ $latestPosts->count() ? \App\Models\Post::query()->published()->count() : 0 }}</strong> {{ __('bài viết') }}</span>
                        </p>
                    </div>
                    <div class="fb-profile__actions">
                        <a class="btn btn-primary" href="{{ route('contact') }}"><i class="fa-regular fa-envelope me-2" aria-hidden="true"></i>{{ __('Nhắn tin') }}</a>
                        @if ($servicesLink)
                            <a class="btn btn-secondary-soft" href="{{ $servicesLink['url'] }}" target="_blank" rel="noopener">{{ $servicesLink['label'] }} <i class="fa-solid fa-arrow-up-right-from-square ms-1" aria-hidden="true"></i></a>
                        @endif
                    </div>
                </div>

                <nav class="fb-profile__tabs" aria-label="{{ __('Điều hướng trang cá nhân') }}">
                    <a class="is-active" href="#toi-la-ai">{{ __('Giới thiệu') }}</a>
                    <a href="#du-an-da-trien-khai">{{ __('Dự án') }}</a>
                    @if ($latestPosts->isNotEmpty())<a href="#bai-viet">{{ __('Blog') }}</a>@endif
                    <a href="#cong-nghe">{{ __('Công nghệ') }}</a>
                    <a href="{{ route('contact') }}">{{ __('Liên hệ') }}</a>
                </nav>
            </article>

            <div class="row g-4 fb-body">
                <aside class="col-lg-4">
                    <div class="fb-card">
                        <h3>{{ __('Giới thiệu') }}</h3>
                        <ul class="fb-info">
                            @if ($website->owner_workplace)<li><i class="fa-solid fa-briefcase" aria-hidden="true"></i><span>{{ __('Làm việc tại') }} <strong>{{ $website->owner_workplace }}</strong></span></li>@endif
                            @if ($website->owner_location)<li><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span>{{ __('Sống tại') }} <strong>{{ $website->owner_location }}</strong></span></li>@endif
                            @if ($servicesLink)<li><i class="fa-solid fa-globe" aria-hidden="true"></i><a href="{{ $servicesLink['url'] }}" target="_blank" rel="noopener">{{ $servicesLink['host'] }}</a></li>@endif
                            @if ($contactEmail)<li><i class="fa-regular fa-envelope" aria-hidden="true"></i><a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></li>@endif
                            @if ($hotline)<li><i class="fa-solid fa-phone" aria-hidden="true"></i><a href="tel:{{ preg_replace('/[^\d+]/', '', $hotline) }}">{{ $hotline }}</a></li>@endif
                        </ul>
                        @include('partials.socials', ['class' => 'fb-socials'])
                    </div>
                </aside>
                <div class="col-lg-8">
                    <div class="fb-card">
                        <h3>{{ __('Về tôi') }}</h3>
                        @if ($website->owner_bio)<p class="fb-bio">{!! nl2br(e($website->owner_bio)) !!}</p>@endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

<h1 class="visually-hidden">{{ $pageTitle }}</h1>

<section class="section-space pf-list" id="du-an-da-trien-khai">
    <div class="container">
        <header class="pf-list__head">
            <h2>{{ __('Khám phá các dự án đã triển khai') }}</h2>
            <p>{{ __('Lọc và xem các dự án thực tế theo lĩnh vực.') }}</p>
        </header>

        @if ($portfolioProjects->isEmpty())
            <p class="empty-state">{{ __('Chưa có dự án được xuất bản.') }}</p>
        @else
            @if ($portfolioFilters->count() > 1)
                <div class="pf-tabs" role="group" aria-label="{{ __('Lọc dự án') }}" data-pf-tabs>
                    <button type="button" class="pf-tab is-active" data-filter="all" aria-pressed="true"><span class="pf-tab__label">{{ __('Tất cả') }}</span><span class="pf-tab__count">{{ $portfolioProjects->count() }}</span></button>
                    @foreach ($portfolioFilters as $filter)
                        <button type="button" class="pf-tab" data-filter="{{ $filter['id'] }}" aria-pressed="false"><span class="pf-tab__label">{{ $filter['name'] }}</span><span class="pf-tab__count">{{ $filter['count'] }}</span></button>
                    @endforeach
                </div>
            @endif

            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4" data-pf-grid>
                @foreach ($portfolioProjects as $project)
                    <div class="col" data-group="{{ $project->filter_group }}">@include('frontend.projects.partials.list-card')</div>
                @endforeach
            </div>
        @endif
    </div>
</section>

@if ($latestPosts->isNotEmpty())
    <section class="section-space pt-0" id="bai-viet">
        <div class="container">
            <header class="pf-list__head">
                <h2>{{ __('Bài viết mới') }}</h2>
                <p>{{ __('Chia sẻ kinh nghiệm làm web, lập trình và vận hành dự án.') }}</p>
            </header>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                @foreach ($latestPosts as $post)
                    <div class="col">@include('frontend.partials.post-card')</div>
                @endforeach
            </div>
            <p class="text-center mt-4"><a class="btn btn-outline-secondary" href="{{ route('posts.index') }}">{{ __('Xem tất cả bài viết') }} <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i></a></p>
        </div>
    </section>
@endif

{{-- Công nghệ & công cụ sử dụng (logo lưu sẵn trong public/images/tech). --}}
@php
    $techRows = [
        [
            'Claude' => 'claude', 'ChatGPT' => 'chatgpt', 'Laravel' => 'laravel', 'Livewire' => 'livewire', 'Filament' => 'filament',
            'PHP' => 'php', 'MySQL' => 'mysql', 'Composer' => 'composer', 'Redis' => 'redis',
        ],
        [
            'Tailwind CSS' => 'tailwindcss', 'Bootstrap' => 'bootstrap', 'Alpine.js' => 'alpinedotjs', 'JavaScript' => 'javascript', 'Vite' => 'vite',
            'Node.js' => 'nodedotjs', 'Git' => 'git', 'GitHub' => 'github', 'Docker' => 'docker', 'Nginx' => 'nginx', 'Cloudflare' => 'cloudflare', 'Figma' => 'figma',
        ],
    ];
@endphp
<section class="section-space pt-0 pf-tech-section" id="cong-nghe">
    <div class="container">
        <header class="pf-list__head">
            <h2>{{ __('Công nghệ sử dụng') }}</h2>
            <p>{{ __('Bộ công cụ dùng để thiết kế, xây dựng và vận hành các dự án.') }}</p>
        </header>
    </div>
    @foreach ($techRows as $row)
        <div class="pf-marquee @if ($loop->odd) pf-marquee--left @else pf-marquee--right @endif">
            @foreach ([false, true] as $duplicate)
                <ul class="pf-marquee__track" @if ($duplicate) aria-hidden="true" @endif>
                    @foreach ($row as $name => $icon)
                        <li class="pf-tech__item">
                            <img src="{{ asset('images/tech/'.$icon.'.svg') }}" alt="" width="32" height="32" loading="lazy">
                            <span>{{ $name }}</span>
                        </li>
                    @endforeach
                </ul>
            @endforeach
        </div>
    @endforeach
</section>

<section class="pb-5">
    <div class="container">
        <div class="pf-cta">
            <div>
                <h2>{{ __('Bạn đang có dự án tương tự?') }}</h2>
                <p>{{ __('Hãy để chúng tôi đồng hành cùng bạn từ ý tưởng đến vận hành.') }}</p>
            </div>
            <div class="d-flex flex-wrap gap-3">
                @if ($hotline)
                    <a class="btn btn-outline-light btn-lg" href="tel:{{ preg_replace('/[^\d+]/', '', $hotline) }}"><i class="fa-solid fa-phone me-2" aria-hidden="true"></i>{{ $hotline }}</a>
                @endif
                @if ($servicesLink)
                    <a class="btn btn-outline-light btn-lg" href="{{ $servicesLink['url'] }}" target="_blank" rel="noopener">{{ $servicesLink['label'] }} <i class="fa-solid fa-arrow-up-right-from-square ms-1" aria-hidden="true"></i></a>
                @endif
                <a class="btn btn-light btn-lg" href="{{ route('contact') }}">{{ __('Nhận tư vấn miễn phí') }}</a>
            </div>
        </div>
    </div>
</section>

<script>
    (() => {
        const tabs = document.querySelector('[data-pf-tabs]');
        const grid = document.querySelector('[data-pf-grid]');
        if (!tabs || !grid) return;
        tabs.addEventListener('click', (event) => {
            const tab = event.target.closest('[data-filter]');
            if (!tab) return;
            tabs.querySelectorAll('[data-filter]').forEach((item) => {
                const on = item === tab;
                item.classList.toggle('is-active', on);
                item.setAttribute('aria-pressed', on);
            });
            grid.querySelectorAll('[data-group]').forEach((card) => {
                card.hidden = tab.dataset.filter !== 'all' && card.dataset.group !== tab.dataset.filter;
            });
        });
    })();
</script>
