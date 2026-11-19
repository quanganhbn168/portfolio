{{-- Trang Portfolio: hero + số liệu, tab lọc theo lĩnh vực, lưới dự án, case study, CTA. --}}
@php
    $hotline = $website->primaryPhone() ?: $website->hotline;
    $heroImage = $heroProject?->image_webp_url;
@endphp

@if (filled($website->owner_name) || filled($website->owner_bio))
    @php
        $ownerImage = \App\Support\Media\MediaUrl::webp($websiteMedia->get($website->owner_media_id), 640);
    @endphp
    <section class="section-space pb-0 pf-owner-top" id="toi-la-ai">
        <div class="container">
            <div class="pf-owner-card">
                @if ($ownerImage)
                    <img class="pf-owner-card__avatar" src="{{ $ownerImage }}" alt="{{ $website->owner_name }}" width="200" height="200" loading="lazy">
                @endif
                <div class="pf-owner-card__body">
                    <span class="pf-badge">{{ __('Tôi là ai') }}</span>
                    <h2>{{ $website->owner_name }}</h2>
                    @if ($website->owner_title)<p class="pf-owner-card__role">{{ $website->owner_title }}</p>@endif
                    @if ($website->owner_bio)<p class="pf-owner-card__bio">{!! nl2br(e($website->owner_bio)) !!}</p>@endif
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        @include('partials.socials', ['class' => 'pf-owner-card__socials'])
                        @if ($servicesLink)
                            <a class="btn btn-primary" href="{{ $servicesLink['url'] }}" target="_blank" rel="noopener">{{ $servicesLink['label'] }} <i class="fa-solid fa-arrow-up-right-from-square ms-1" aria-hidden="true"></i></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

<section class="pf-hero">
    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-6">
                <span class="pf-badge">{{ __('Portfolio') }}</span>
                <h1 class="pf-hero__title">{{ $pageTitle }}</h1>
                <p class="pf-hero__lead">{{ $pageDescription }}</p>

                @if ($servicesLink)
                    <p class="pf-owner"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> {{ __('Đây là portfolio của') }} <a href="{{ $servicesLink['url'] }}" target="_blank" rel="noopener">{{ $servicesLink['host'] }}</a> — {{ __('xem bảng giá và dịch vụ chi tiết tại đó.') }}</p>
                @endif
                <div class="d-flex flex-wrap gap-3">
                    <a class="btn btn-primary btn-lg" href="{{ route('contact') }}">{{ __('Liên hệ tư vấn dự án') }} <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i></a>
                    @if ($servicesLink)
                        <a class="btn btn-outline-secondary btn-lg" href="{{ $servicesLink['url'] }}" target="_blank" rel="noopener">{{ $servicesLink['label'] }} <i class="fa-solid fa-arrow-up-right-from-square ms-1" aria-hidden="true"></i></a>
                    @endif
                </div>
            </div>
            @if ($heroImage)
                <div class="col-lg-6">
                    <div class="pf-hero__media"><img src="{{ $heroImage }}" alt="{{ $heroProject->title }}" fetchpriority="high"></div>
                </div>
            @endif
        </div>
    </div>
</section>

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
