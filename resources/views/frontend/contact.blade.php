@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/contact.css')
@endpush

@section('content')
    <section class="contact-page-hero" data-system-page="contact">
        @if ($pageBannerUrl)
            <img
                class="contact-page-hero__image"
                data-page-banner-image
                src="{{ $pageBannerUrl }}"
                alt="{{ $page['title'] }}"
            >
        @endif

        <div class="contact-page-hero__overlay"></div>

        <div class="container">
            <nav class="contact-page-hero__breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">{{ __('Trang chủ') }}</a>

                <span aria-hidden="true">/</span>

                <span aria-current="page">{{ __('Liên hệ') }}</span>
            </nav>

            <h1>{{ $page['title'] }}</h1>

            <p class="contact-page-hero__intro">
                @if (filled($page['excerpt']))
                    {{ $page['excerpt'] }}
                @else
                    {{ __(':p1 luôn sẵn sàng lắng nghe và tư vấn giải pháp decal, logo phù hợp cho từng dự án.', ['p1' => $website->site_name]) }}
                @endif
            </p>
        </div>
    </section>

    <section class="section-space">
        <div class="container contact-layout">

            {{-- CONTACT INFO --}}
            <div>
                <aside class="contact-details">

                    <h2 class="h2 contact-details__title">
                        {{ __('Liên hệ với chúng tôi') }}
                    </h2>

                    <div class="contact-details__list">

                        {{-- PHONE --}}
                        @if ($contactPhones->isNotEmpty())
                            <div class="contact-info-item">
                                <div class="contact-info-item__icon">
                                    <i class="fa-solid fa-phone"></i>
                                </div>

                                <div class="contact-info-item__content">
                                    <div class="contact-info-item__label">
                                        {{ __('Điện thoại') }}
                                    </div>

                                    <div class="contact-info-item__phones">
                                        @foreach ($contactPhones as $phone)
                                            @if (! $loop->first)
                                                <span class="contact-info-item__separator" aria-hidden="true">
                                                    -
                                                </span>
                                            @endif

                                            <a href="{{ $phone['href'] }}">
                                                {{ $phone['label'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif


                        {{-- EMAIL --}}
                        @if ($website->contact_email)
                            <div class="contact-info-item">
                                <div class="contact-info-item__icon">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>

                                <div class="contact-info-item__content">
                                    <div class="contact-info-item__label">
                                        Email
                                    </div>

                                    <a
                                        class="contact-info-item__value text-break"
                                        href="mailto:{{ $website->contact_email }}"
                                    >
                                        {{ $website->contact_email }}
                                    </a>
                                </div>
                            </div>
                        @endif

                    </div>
                </aside>


                {{-- ĐỊA ĐIỂM: chọn một chi nhánh thì bản đồ chuyển sang địa chỉ đó --}}
                <div class="contact-locations" data-locations>
                    @if ($contactBranches->isNotEmpty())
                        <h2 class="h4 contact-locations__title">{{ __('Các địa điểm') }}</h2>

                        <div class="contact-locations__list">
                            @foreach ($contactBranches as $branch)
                                <button
                                    type="button"
                                    class="contact-branch {{ $loop->first ? 'is-active' : '' }}"
                                    data-map="{{ $branch['map'] }}"
                                    aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                                >
                                    <span class="contact-branch__icon"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>
                                    <span class="contact-branch__body">
                                        <strong>{{ $branch['name'] }}</strong>
                                        <span>{{ $branch['address'] }}</span>
                                        @if ($branch['phone'])
                                            <span class="contact-branch__phone"><i class="fa-solid fa-phone" aria-hidden="true"></i>{{ $branch['phone'] }}</span>
                                        @endif
                                    </span>
                                    <span class="contact-branch__state">{{ __('Đang xem trên bản đồ') }}</span>
                                </button>
                            @endforeach
                        </div>

                        <div class="contact-page__map" aria-label="{{ __('Bản đồ vị trí :p1', ['p1' => $website->company_name ?: $website->site_name]) }}">
                            <iframe
                                data-map-frame
                                src="{{ $contactBranches->first()['map'] }}"
                                title="{{ __('Bản đồ vị trí :p1', ['p1' => $website->company_name ?: $website->site_name]) }}"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                allowfullscreen
                            ></iframe>
                        </div>
                    @else
                        <div class="contact-page__map">
                            <div class="contact-page__map-empty">
                                <i class="fa-solid fa-map-location-dot me-2"></i>
                                {{ __('Bản đồ chưa được cấu hình trong Cài đặt chung.') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>


            {{-- CONTACT FORM --}}
            <form
                class="contact-form"
                method="POST"
                action="{{ route('contact.store') }}"
            >
                @csrf
                <x-honeypot />

                <div class="row g-3">

                    <label class="fw-semibold col-md-6">
                        {{ __('Họ và tên') }} <span class="text-danger">*</span>

                        <input
                            class="form-control"
                            name="name"
                            value="{{ old('name') }}"
                            required
                        >
                    </label>

                    <label class="fw-semibold col-md-6">
                        {{ __('Số điện thoại') }} <span class="text-danger">*</span>

                        <input
                            class="form-control"
                            name="phone"
                            type="tel"
                            autocomplete="tel"
                            inputmode="tel"
                            pattern="[0-9+().\s\-]{9,20}"
                            title="{{ __('Số điện thoại Việt Nam, ví dụ 0901 234 567') }}"
                            value="{{ old('phone') }}"
                            required
                        >
                    </label>

                    <label class="fw-semibold col-md-6">
                        Email

                        <input
                            class="form-control"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                        >
                    </label>

                    <label class="fw-semibold col-md-6">
                        {{ __('Tên công ty') }}

                        <input
                            class="form-control"
                            name="company"
                            autocomplete="organization"
                            maxlength="255"
                            value="{{ old('company') }}"
                        >
                    </label>

                    <label class="fw-semibold col-12">
                        {{ __('Nội dung') }} <span class="text-danger">*</span>

                        <textarea
                            class="form-control"
                            name="message"
                            rows="7"
                            required
                        >{{ old('message') }}</textarea>
                    </label>

                </div>

                @if ($errors->any())
                    <p class="alert alert-danger mt-3">
                        {{ $errors->first() }}
                    </p>
                @endif

                <button class="btn btn-primary mt-3" type="submit">
                    {{ __('Gửi yêu cầu') }}
                    <i class="fa-solid fa-arrow-right ms-2"></i>
                </button>
            </form>

        </div>
    </section>

    <script>
        document.querySelectorAll('[data-locations]').forEach(function (box) {
            var frame = box.querySelector('[data-map-frame]');
            var buttons = box.querySelectorAll('.contact-branch');
            buttons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    buttons.forEach(function (b) {
                        b.classList.toggle('is-active', b === btn);
                        b.setAttribute('aria-pressed', b === btn ? 'true' : 'false');
                    });
                    if (frame && frame.getAttribute('src') !== btn.getAttribute('data-map')) {
                        frame.setAttribute('src', btn.getAttribute('data-map'));
                    }
                });
            });
        });
    </script>
@endsection
