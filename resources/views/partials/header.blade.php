<header class="header-sticky" data-site-header aria-label="{{ __('Đầu trang :p1', ['p1' => $website->site_name]) }}">
    <div class="header">
        <div class="container header__inner">
            @include('partials.header.brand')
            <div class="d-none d-xl-block flex-grow-1 header__navigation">
                @include('partials.header.navigation')
            </div>
            @include('partials.header.actions')
        </div>
    </div>
</header>
{{-- Overlays stay outside the sticky/transformed header to preserve stacking and focus. --}}
@include('partials.header.mobile-menu')
