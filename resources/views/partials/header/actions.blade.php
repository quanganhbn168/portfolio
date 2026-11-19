<div class="header__actions">
    @include('partials.language-switcher')
    @if ($servicesLink)
        <a class="btn btn-outline-secondary header__services d-none d-lg-inline-flex" href="{{ $servicesLink['url'] }}" target="_blank" rel="noopener">{{ $servicesLink['label'] }} <i class="fa-solid fa-arrow-up-right-from-square ms-1" aria-hidden="true"></i></a>
    @endif
    <a class="btn btn-primary header__cta" href="{{ route('contact') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>{{ __('Liên hệ với chúng tôi') }}</a>
    <button class="header__toggle d-xl-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobile-drawer" aria-controls="mobile-drawer" aria-label="{{ __('Mở menu') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
    </button>
</div>
