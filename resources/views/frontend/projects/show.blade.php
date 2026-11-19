@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/project.css')
@endpush

@section('content')
<div class="project-case">
    <section class="project-case__hero">
        @if ($project->image_url ?: $defaultBannerUrl)<img class="project-case__hero-image" src="{{ $project->image_webp_url ?: $defaultBannerUrl }}" alt="" fetchpriority="high">@endif
        <div class="container position-relative">
            <nav class="project-case__breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">{{ __('Trang chủ') }}</a><span>/</span><a href="{{ route('projects.index') }}">{{ __('Dự án') }}</a><span>/</span>@if ($project->category)<a href="{{ route('slug.show', ['slug' => $project->category->slug]) }}">{{ $project->category->name }}</a><span>/</span>@endif<span aria-current="page">{{ $project->title }}</span>
            </nav>
            <div class="project-case__hero-content">
                <div>
                    @if ($project->category)<span class="project-case__badge">{{ $project->category->name }}</span>@endif
                    <h1 class="text-white">{{ $project->title }}</h1>
                    @if ($project->excerpt)<p>{{ $project->excerpt }}</p>@endif
                    @if ($project->website_url)<a class="btn btn-primary btn-lg mt-3" href="{{ $project->website_url }}" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-arrow-up-right-from-square me-2" aria-hidden="true"></i>{{ __('Xem website thực tế') }}</a>@endif
                </div>
            </div>
        </div>
    </section>

    <nav class="project-case__nav" aria-label="{{ __('Điều hướng nội dung dự án') }}">
        <div class="container">
            <a href="#tong-quan"><i class="fa-regular fa-clipboard" aria-hidden="true"></i>{{ __('Tổng quan') }}</a>
            @if (count($content['solution']['items']) || filled($content['solution']['description'] ?? null) || $content['solution']['image_url'])<a href="#giai-phap-du-an"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i>{{ __('Giải pháp triển khai') }}</a>@endif
            @if (count($content['scope']['items']))<a href="#hang-muc"><i class="fa-solid fa-gears" aria-hidden="true"></i>{{ __('Hạng mục thi công') }}</a>@endif
            @if (count($content['construction']['images']))<a href="#hinh-anh-du-an"><i class="fa-regular fa-image" aria-hidden="true"></i>{{ __('Hình ảnh thực tế') }}</a>@endif
            @if ($relatedProjects->isNotEmpty())<a href="#du-an-lien-quan"><i class="fa-solid fa-diagram-project" aria-hidden="true"></i>{{ __('Dự án liên quan') }}</a>@endif
        </div>
    </nav>

    <section id="tong-quan" class="project-case__section">
        <div class="container">
            <div class="row g-4 g-xl-5 align-items-start">
                <div class="{{ count($content['gallery']) ? 'col-lg-5' : 'col-12' }}">
                    @include('frontend.projects.partials.section-heading', ['section' => ['label' => __('Tổng quan dự án'), 'title' => __('Thông tin chung')]])
                    <div class="article-prose">{!! $content['overview']['body_html'] !!}</div>
                    @if ($projectVideoUrl)<a href="{{ $projectVideoUrl }}" class="section-link" target="_blank" rel="noopener">{{ __('Xem video dự án →') }}</a>@endif
                    @if (count($content['overview']['facts']))<dl class="project-case__facts">
                        @foreach ($content['overview']['facts'] as $fact)
                            <div><dt>{{ $fact['label'] }}</dt><dd>{{ $fact['value'] }}</dd></div>
                        @endforeach
                    </dl>@endif
                </div>
                @if (count($content['gallery']))
                <div class="col-lg-7">
                    <div class="project-case__gallery">
                        <div class="swiper" data-project-gallery aria-label="{{ __('Ảnh tổng quan dự án') }}">
                            <div class="swiper-wrapper">
                                @foreach ($content['gallery'] as $image)
                                    <div class="swiper-slide"><img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}" loading="lazy"></div>
                                @endforeach
                            </div>
                            <button type="button" class="project-case__arrow project-case__arrow--prev" data-project-prev aria-label="{{ __('Ảnh trước') }}"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                            <button type="button" class="project-case__arrow project-case__arrow--next" data-project-next aria-label="{{ __('Ảnh tiếp theo') }}"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
                        </div>
                        <div class="swiper project-case__thumbs" data-project-thumbs aria-label="{{ __('Chọn ảnh dự án') }}">
                            <div class="swiper-wrapper">
                                @foreach ($content['gallery'] as $image)
                                    <div class="swiper-slide"><button type="button" aria-label="Xem {{ $image['alt'] }}"><img src="{{ $image['url'] }}" alt="" loading="lazy"></button></div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>

    @if (count($content['challenges']['items']))
    <section class="project-case__section project-case__band">
        <div class="container row-gap-4 project-case__band-grid">
            <div>@include('frontend.projects.partials.section-heading', ['section' => ['label' => __('Yêu cầu & bài toán'), 'title' => __('Thách thức của dự án')]])</div>
            <div class="project-case__tiles project-case__tiles--four">
                @foreach ($content['challenges']['items'] as $item)
                    <article class="project-case__tile"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i><h3>{{ $item['title'] }}</h3>@if (filled($item['description'] ?? null))<p>{{ $item['description'] }}</p>@endif</article>
                @endforeach
            </div>
        </div>
    </section>

    @endif
    @if (count($content['solution']['items']) || filled($content['solution']['description'] ?? null) || $content['solution']['image_url'])
    <section id="giai-phap-du-an" class="project-case__section">
        <div class="container row-gap-4 project-case__solution">
            <div>@include('frontend.projects.partials.section-heading', ['section' => ['label' => __('Giải pháp triển khai'), 'title' => __('Giải pháp toàn diện')]])<p>{{ $content['solution']['description'] ?? '' }}</p><a class="btn btn-primary" href="{{ route('contact') }}">{{ __('Liên hệ tư vấn giải pháp') }} <i class="fa-solid fa-arrow-right ms-2" aria-hidden="true"></i></a></div>
            <ul class="project-case__solutions">
                @foreach ($content['solution']['items'] as $item)<li><i class="fa-solid fa-circle-check" aria-hidden="true"></i>{{ $item['text'] }}</li>@endforeach
            </ul>
            @if ($content['solution']['image_url'])
                <figure class="project-case__solution-image mb-0"><img src="{{ $content['solution']['image_url'] }}" alt="{{ $project->title }}" loading="lazy"></figure>
            @endif
        </div>
    </section>

    @endif
    @if (count($content['scope']['items']))
    <section id="hang-muc" class="project-case__section project-case__section--line">
        <div class="container">
            @include('frontend.projects.partials.section-heading', ['section' => ['label' => __('Hạng mục thi công'), 'title' => __('Các hạng mục chính')]])
            <div class="project-case__tiles project-case__tiles--six">
                @foreach ($content['scope']['items'] as $item)
                    <article class="project-case__tile"><i class="fa-solid fa-gears" aria-hidden="true"></i><h3>{{ $item['title'] }}</h3>@if (filled($item['description'] ?? null))<p>{{ $item['description'] }}</p>@endif</article>
                @endforeach
            </div>
        </div>
    </section>

    @endif
    @if (count($content['construction']['images']))
    <section id="hinh-anh-du-an" class="project-case__section project-case__section--line">
        <div class="container">
            @include('frontend.projects.partials.section-heading', ['section' => ['label' => __('Hình ảnh thực tế'), 'title' => __('Một số hình ảnh trong quá trình thi công')]])
            <div class="project-case__slider-shell">
                <div class="swiper" data-project-site-swiper>
                    <div class="swiper-wrapper">
                        @foreach ($content['construction']['images'] as $image)
                            <div class="swiper-slide"><a href="{{ $image['url'] }}" target="_blank" rel="noopener" aria-label="{{ __('Mở ảnh :p1', ['p1' => $image['alt']]) }}"><img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}" loading="lazy"></a></div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                <button type="button" class="project-case__arrow project-case__arrow--prev" data-strip-prev aria-label="{{ __('Nhóm ảnh trước') }}"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                <button type="button" class="project-case__arrow project-case__arrow--next" data-strip-next aria-label="{{ __('Nhóm ảnh tiếp theo') }}"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
            </div>
        </div>
    </section>

    @endif
    @if ($relatedProjects->isNotEmpty())
    <section id="du-an-lien-quan" class="project-case__section">
        <div class="container">
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4"><div>@include('frontend.projects.partials.section-heading', ['section' => ['label' => __('Dự án liên quan'), 'title' => __('Có thể bạn quan tâm')]])</div><a class="section-link" href="{{ route('projects.index') }}">{{ __('Xem tất cả dự án →') }}</a></div>
            @if ($relatedProjects->isNotEmpty())
                <div class="project-case__slider-shell">
                    <div class="swiper" data-project-related-swiper><div class="swiper-wrapper">
                        @foreach ($relatedProjects as $relatedProject)<div class="swiper-slide">@include('frontend.partials.project-card', ['project' => $relatedProject])</div>@endforeach
                    </div></div>
                    <button type="button" class="project-case__arrow project-case__arrow--prev" data-strip-prev aria-label="{{ __('Dự án trước') }}">‹</button>
                    <button type="button" class="project-case__arrow project-case__arrow--next" data-strip-next aria-label="{{ __('Dự án tiếp theo') }}">›</button>
                </div>
            @endif
        </div>
    </section>

    @endif
</div>
@endsection
