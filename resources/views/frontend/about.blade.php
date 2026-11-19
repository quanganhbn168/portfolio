@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/about.css')
@endpush

@php
    $heroImageUrl = $pageBannerUrl ?: $about['image_url'];
    // Từ cuối của tiêu đề được tô xanh lá (About Us -> About <Us>).
    $titleWords = preg_split('/\s+/', trim($about['title']));
    $titleLast = count($titleWords) > 1 ? array_pop($titleWords) : null;
    $titleFirst = implode(' ', $titleWords);
    $hasStory = filled(trim(strip_tags($about['story']))) || $about['stats'] !== [];
    $hasLeader = filled($about['leader_title']) || filled(trim(strip_tags($about['leader_message'])));
    $principles = array_values(array_filter([
        filled($about['vision']) ? ['class' => 'about-card--vision', 'icon' => 'fa-bullseye', 'title' => $about['vision_title'] ?: __('Tầm nhìn'), 'text' => $about['vision']] : null,
        filled($about['mission']) ? ['class' => 'about-card--mission', 'icon' => 'fa-rocket', 'title' => $about['mission_title'] ?: __('Sứ mệnh'), 'text' => $about['mission']] : null,
    ]));
@endphp

@section('content')
<div data-system-page="about" class="about-summary">
    {{-- HERO: nền xanh, chữ bên trái, ảnh bên phải cắt chéo kèm dải xanh lá --}}
    <section class="about-hero">
        @if ($heroImageUrl)
            <div class="about-hero__media" aria-hidden="true">
                <img @if ($pageBannerUrl) data-page-banner-image @endif src="{{ $heroImageUrl }}" alt="" fetchpriority="high">
            </div>
        @endif
        <div class="container about-hero__inner">
            <nav aria-label="Breadcrumb">
                <ol class="about-hero__crumbs">
                    <li><a href="{{ route('home') }}">{{ __('Trang chủ') }}</a></li>
                    <li aria-hidden="true">/</li>
                    <li aria-current="page">{{ $about['title'] }}</li>
                </ol>
            </nav>
            <h1>{{ $titleFirst }}@if ($titleLast) <span>{{ $titleLast }}</span>@endif</h1>
            @if ($about['intro'])
                <p class="about-hero__intro">{!! nl2br(e($about['intro'])) !!}</p>
            @endif
            @if ($about['video'])
                <button class="btn btn-primary mt-3" type="button" data-bs-toggle="modal" data-bs-target="#about-video-modal">
                    <i class="fa-solid fa-play me-2" aria-hidden="true"></i>{{ $about['hero_video_label'] ?: __('Xem video giới thiệu') }}
                </button>
            @endif
        </div>
    </section>

    {{-- TỔNG QUAN CÔNG TY: ảnh trái, nội dung + số liệu phải --}}
    @if ($hasStory)
        <section class="about-section about-story">
            <div class="container about-story__grid">
                @if ($about['story_image_url'])
                    <div class="about-photo">
                        <img src="{{ $about['story_image_url'] }}" alt="{{ $about['story_title'] ?: $about['title'] }}" loading="lazy">
                        @if ($about['video'])
                            <button class="about-photo__play" type="button" data-bs-toggle="modal" data-bs-target="#about-video-modal" aria-label="{{ __('Xem video giới thiệu') }}">
                                <i class="fa-solid fa-play" aria-hidden="true"></i>
                            </button>
                        @endif
                    </div>
                @endif
                <div class="about-story__text">
                    @if ($about['story_eyebrow'])<p class="about-eyebrow">{{ $about['story_eyebrow'] }}</p>@endif
                    @if ($about['story_title'])<h2 class="about-title">{{ $about['story_title'] }}</h2>@endif
                    @if (filled(trim(strip_tags($about['story']))))
                        <div class="about-prose">{!! $about['story'] !!}</div>
                    @endif
                    @if ($about['stats'])
                        <ul class="about-stats">
                            @foreach ($about['stats'] as $stat)
                                <li>
                                    <i class="fa-solid {{ $stat['icon'] }}" aria-hidden="true"></i>
                                    <strong>{{ $stat['value'] }}</strong>
                                    <span>{{ $stat['label'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    @if ($about['services_link_label'])
                        <a class="btn btn-primary mt-4" href="{{ route('home') }}">{{ $about['services_link_label'] }} <i class="fa-solid fa-arrow-right ms-2" aria-hidden="true"></i></a>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- THƯ CỦA LÃNH ĐẠO: nội dung trái, ảnh phải --}}
    @if ($hasLeader)
        <section class="about-section about-leader">
            <div class="container about-leader__grid">
                <div class="about-leader__text">
                    @if ($about['leader_eyebrow'])<p class="about-eyebrow">{{ $about['leader_eyebrow'] }}</p>@endif
                    @if ($about['leader_title'])<h2 class="about-title">{{ $about['leader_title'] }}</h2>@endif
                    <div class="about-prose about-letter">{!! $about['leader_message'] !!}</div>
                    @if ($about['leader_signature_url'])
                        <img class="about-signature" src="{{ $about['leader_signature_url'] }}" alt="" loading="lazy">
                    @endif
                    @if ($about['leader_name'])<p class="about-signer">{{ $about['leader_name'] }}</p>@endif
                </div>
                @if ($about['leader_image_url'])
                    <figure class="about-leader__photo">
                        <img src="{{ $about['leader_image_url'] }}" alt="{{ $about['leader_title'] ?: $about['title'] }}" loading="lazy">
                    </figure>
                @endif
            </div>
        </section>
    @endif

    {{-- TẦM NHÌN / SỨ MỆNH / GIÁ TRỊ CỐT LÕI: ba thẻ --}}
    @if ($principles || $about['values'])
        <section class="about-section about-principles">
            <div class="container about-cards">
                @foreach ($principles as $principle)
                    <article class="about-card {{ $principle['class'] }}">
                        <h2><i class="fa-solid {{ $principle['icon'] }}" aria-hidden="true"></i>{{ $principle['title'] }}</h2>
                        <p>{!! nl2br(e($principle['text'])) !!}</p>
                    </article>
                @endforeach
                @if ($about['values'])
                    <article class="about-card about-card--values">
                        <h2><i class="fa-solid fa-gem" aria-hidden="true"></i>{{ $about['principles_title'] ?: __('Giá trị cốt lõi') }}</h2>
                        <ul>
                            @foreach ($about['values'] as $value)
                                <li>{{ $value['title'] }}</li>
                            @endforeach
                        </ul>
                    </article>
                @endif
            </div>
        </section>
    @endif

    {{-- LỊCH SỬ: timeline ngang --}}
    @if ($historyTimeline->isNotEmpty())
        <section class="about-section about-history">
            <div class="container">
                @if ($about['history_eyebrow'])<p class="about-eyebrow">{{ $about['history_eyebrow'] }}</p>@endif
                @if ($about['history_title'])<h2 class="about-title">{{ $about['history_title'] }}</h2>@endif
                @if ($about['history_description'])<p class="about-history__lead">{{ $about['history_description'] }}</p>@endif
                <ol class="about-timeline">
                    @foreach ($historyTimeline as $milestone)
                        <li class="about-timeline__item">
                            <span class="about-timeline__dot" aria-hidden="true"></span>
                            <div class="about-timeline__card">
                                <i class="fa-solid {{ $milestone['icon'] }}" aria-hidden="true"></i>
                                <strong class="about-timeline__year">{{ $milestone['year'] }}</strong>
                                @if ($milestone['title'])<h3>{{ $milestone['title'] }}</h3>@endif
                                @if ($milestone['description'])<p>{{ $milestone['description'] }}</p>@endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @elseif (filled($about['history']))
        <section class="about-section about-history">
            <div class="container">
                @if ($about['history_title'])<h2 class="about-title">{{ $about['history_title'] }}</h2>@endif
                <p class="about-history__lead">{!! nl2br(e($about['history'])) !!}</p>
            </div>
        </section>
    @endif

    {{-- VĂN HÓA DOANH NGHIỆP (tuỳ chọn): chỉ hiện khi có nội dung --}}
    @if ($about['culture_title'] || $about['culture_items'])
        <section class="about-section">
            <div class="container">
                <h2 class="about-title">{{ $about['culture_title'] }}</h2>
                @if ($about['culture_description'])<p class="about-history__lead">{!! nl2br(e($about['culture_description'])) !!}</p>@endif
                <div class="about-culture">
                    @foreach ($about['culture_items'] as $item)
                        <div class="about-culture__item">
                            <i class="fa-solid {{ $item['icon'] }}" aria-hidden="true"></i>
                            <h3>{{ $item['title'] }}</h3>
                            @if ($item['description'])<p>{{ $item['description'] }}</p>@endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- KHÔNG GIAN VĂN PHÒNG (tuỳ chọn) --}}
    @if ($about['office_gallery']->isNotEmpty())
        <section class="about-section">
            <div class="container">
                <div class="about-slider-head">
                    <div>
                        @if ($about['office_title'])<h2 class="about-title mb-2">{{ $about['office_title'] }}</h2>@endif
                        @if ($about['office_description'])<p class="mb-0">{!! nl2br(e($about['office_description'])) !!}</p>@endif
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary" data-about-swiper-prev aria-label="{{ __('Ảnh trước') }}"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i></button>
                        <button type="button" class="btn btn-outline-primary" data-about-swiper-next aria-label="{{ __('Ảnh tiếp theo') }}"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                    </div>
                </div>
                <div class="swiper" data-about-swiper="office">
                    <div class="swiper-wrapper">
                        @foreach ($about['office_gallery'] as $image)
                            <div class="swiper-slide">
                                <figure class="about-office-gallery__item">
                                    <img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}" loading="lazy">
                                </figure>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>

@if ($about['video'])
    <div class="modal fade" id="about-video-modal" tabindex="-1" aria-label="{{ __('Video giới thiệu') }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="{{ __('Đóng') }}"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="about-intro-video" data-video-source="{{ $about['video']['source'] }}" data-video-url="{{ $about['video']['url'] }}"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        (() => {
            const modal = document.getElementById('about-video-modal');
            const holder = modal.querySelector('.about-intro-video');
            modal.addEventListener('shown.bs.modal', () => {
                const el = document.createElement(holder.dataset.videoSource === 'youtube' ? 'iframe' : 'video');
                el.src = holder.dataset.videoUrl;
                if (el.tagName === 'IFRAME') {
                    el.allow = 'autoplay; fullscreen; picture-in-picture';
                    el.allowFullscreen = true;
                    el.title = 'Video giới thiệu';
                } else {
                    el.controls = true;
                    el.autoplay = true;
                }
                holder.replaceChildren(el);
            });
            modal.addEventListener('hidden.bs.modal', () => holder.replaceChildren());
        })();
    </script>
@endif
@endsection
