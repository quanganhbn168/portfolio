@extends('layouts.master')

@push('styles')
    @vite('resources/css/frontend/pages/blog.css')
@endpush

@section('content')
    <section class="blog-head">
        <div class="container">
            <span class="pf-badge">{{ __('Dịch vụ') }}</span>
            <h1>{{ $page['title'] }}</h1>
            @if (filled($page['excerpt']))<p>{{ $page['excerpt'] }}</p>@endif
        </div>
    </section>

    <section class="section-space" id="dich-vu">
        <div class="container">
            <header class="pf-list__head">
                <h2>{{ __('Dịch vụ cung cấp') }}</h2>
            </header>
            @if ($services->isEmpty())
                <p class="empty-state">{{ __('Đang cập nhật dịch vụ.') }}</p>
            @else
                @include('frontend.partials.services-grid')
            @endif
        </div>
    </section>

    <section class="section-space pt-0" id="bang-gia">
        <div class="container">
            <header class="pf-list__head">
                <h2>{{ __('Bảng giá tham khảo') }}</h2>
                <p>{{ __('Giá thực tế tuỳ theo yêu cầu, liên hệ để được báo giá chi tiết.') }}</p>
            </header>
            @if ($plans->isEmpty())
                <p class="empty-state">{{ __('Đang cập nhật bảng giá.') }}</p>
            @else
                @include('frontend.partials.pricing-plans')
            @endif
        </div>
    </section>

    <section class="pb-5">
        <div class="container">
            <div class="pf-cta">
                <div>
                    <h2>{{ __('Chưa thấy gói phù hợp?') }}</h2>
                    <p>{{ __('Mô tả nhu cầu của bạn, mình sẽ tư vấn và báo giá riêng.') }}</p>
                </div>
                <button type="button" class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#quote-modal">{{ __('Nhận tư vấn miễn phí') }}</button>
            </div>
        </div>
    </section>
@endsection
