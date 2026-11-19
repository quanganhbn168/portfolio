{{-- Trang Portfolio: hero + số liệu, tab lọc theo lĩnh vực, lưới dự án, case study, CTA. --}}
@php
    $hotline = $website->primaryPhone() ?: $website->hotline;
    $heroImage = $caseStudy?->image_webp_url;
    $stats = collect([
        ['value' => $portfolioStats['projects'], 'label' => __('Dự án triển khai'), 'icon' => 'fa-layer-group'],
        ['value' => $portfolioStats['fields'], 'label' => __('Lĩnh vực'), 'icon' => 'fa-shapes'],
        ['value' => $portfolioStats['clients'], 'label' => __('Khách hàng'), 'icon' => 'fa-handshake'],
    ])->filter(fn (array $stat): bool => $stat['value'] > 0);
@endphp

<section class="pf-hero">
    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-6">
                <span class="pf-badge">{{ __('Portfolio') }}</span>
                <h1 class="pf-hero__title">{{ $pageTitle }}</h1>
                <p class="pf-hero__lead">{{ $pageDescription }}</p>

                @if ($stats->isNotEmpty())
                    <ul class="pf-stats">
                        @foreach ($stats as $stat)
                            <li>
                                <i class="fa-solid {{ $stat['icon'] }}" aria-hidden="true"></i>
                                <span><strong>{{ $stat['value'] }}+</strong>{{ $stat['label'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="d-flex flex-wrap gap-3">
                    <a class="btn btn-primary btn-lg" href="{{ route('contact') }}">{{ __('Liên hệ tư vấn dự án') }} <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i></a>
                    @if ($caseStudy)
                        <a class="btn btn-outline-secondary btn-lg" href="#case-study">{{ __('Xem case study') }}</a>
                    @endif
                </div>
            </div>
            @if ($heroImage)
                <div class="col-lg-6">
                    <div class="pf-hero__media"><img src="{{ $heroImage }}" alt="{{ $caseStudy->title }}" fetchpriority="high"></div>
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
                    <button type="button" class="pf-tab is-active" data-filter="all" aria-pressed="true">{{ __('Tất cả') }} ({{ $portfolioProjects->count() }})</button>
                    @foreach ($portfolioFilters as $filter)
                        <button type="button" class="pf-tab" data-filter="{{ $filter['id'] }}" aria-pressed="false">{{ $filter['name'] }} ({{ $filter['count'] }})</button>
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

@if ($caseStudy)
    <section class="section-space pt-0" id="case-study">
        <div class="container">
            <div class="pf-case">
                <div class="pf-case__text">
                    <span class="pf-badge">{{ __('Case study') }}</span>
                    <h2>{{ $caseStudy->title }}</h2>
                    @if ($caseStudy->excerpt)
                        <p>{{ $caseStudy->excerpt }}</p>
                    @endif
                    <a class="btn btn-primary" href="{{ route('slug.show', ['slug' => $caseStudy->slug]) }}">{{ __('Xem chi tiết case study') }} <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i></a>
                </div>
                @if ($heroImage)
                    <div class="pf-case__media"><img src="{{ $caseStudy->image_card_url }}" alt="" loading="lazy"></div>
                @endif
            </div>
        </div>
    </section>
@endif

<section class="pb-5">
    <div class="container">
        <div class="pf-cta">
            <div>
                <h2>{{ __('Bạn đang có dự án tương tự?') }}</h2>
                <p>{{ __('Hãy để chúng tôi đồng hành cùng bạn từ ý tưởng đến vận hành.') }}</p>
            </div>
            <div class="d-flex flex-wrap gap-3">
                @if ($hotline)
                    <a class="btn btn-light btn-lg" href="tel:{{ preg_replace('/[^\d+]/', '', $hotline) }}"><i class="fa-solid fa-phone me-2" aria-hidden="true"></i>{{ $hotline }}</a>
                @endif
                <a class="btn btn-outline-light btn-lg" href="{{ route('contact') }}">{{ __('Nhận tư vấn miễn phí') }}</a>
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
