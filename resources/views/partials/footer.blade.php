<footer class="footer">
    <div class="container footer__main">
        <div class="row g-4 g-lg-5">
        <section class="col-12 col-md-6 col-lg-5">
            <a class="footer__logo" href="{{ route('home') }}" aria-label="{{ $website->site_name }}">
                @if ($websiteMediaUrls->get($website->logo_media_id))
                    <img src="{{ $websiteMediaUrls->get($website->logo_media_id) }}" alt="{{ $website->site_name }}">
                @else
                    <span>{{ $website->site_name }}</span>
                @endif
            </a>
            <p class="footer__tagline">{{ $website->footer_description }}</p>
            @include('partials.socials')
        </section>

        <section class="col-12 col-md-6 col-lg-2">
            <h2 class="footer__heading">{{ __('Liên kết nhanh') }}</h2>
            <nav class="footer__links" aria-label="{{ __('Liên kết chân trang') }}">
                @if ($footerNavigation->isNotEmpty())
                    @foreach ($footerNavigation as $item)
                        <div >
                            <a href="{{ $item['url'] }}" @if ($item['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif>{{ $item['label'] }}</a>
                            @if ($item['has_children'])<div class="footer__sub-links">@foreach ($item['children'] as $child)<a href="{{ $child['url'] }}" @if ($child['target'] === '_blank') target="_blank" rel="noopener noreferrer" @endif>{{ $child['label'] }}</a>@endforeach</div>@endif
                        </div>
                    @endforeach
                @else
                    <a href="{{ route('home') }}">{{ __('Trang chủ') }}</a><a href="{{ route('about') }}">{{ __('Giới thiệu') }}</a>@foreach ($footerPages ?? [] as $footerPage)<a href="{{ $footerPage->url }}">{{ $footerPage->title }}</a>@endforeach<a href="{{ url('/') }}#lien-he">{{ __('Liên hệ') }}</a>
                @endif
            </nav>
        </section>

        <section class="col-12 col-md-6 col-lg-5">
            <h2 class="footer__heading">{{ __('Thông tin liên hệ') }}</h2>
            <div class="footer__contacts">
                @if ($footerContactBranches->isNotEmpty())
                    @foreach ($footerContactBranches as $branch)<p><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg><span>{{ $branch['address'] }}</span></p>@endforeach
                @elseif ($website->address)
                    <p><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg><span>{{ $website->address }}</span></p>
                @endif
                @if ($footerContactPhones->isNotEmpty())<p><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.2 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.64a2 2 0 0 1-.45 2.11L8 9.75a16 16 0 0 0 6 6l1.28-1.28a16 16 0 0 1 2.11-.45c.86.29 1.74.5 2.64.62A2 2 0 0 1 22 16.92Z"/></svg><span class="footer__phone-list">@foreach ($footerContactPhones as $phone)<a href="{{ $phone['href'] }}">{{ $phone['label'] }}</a>@if (! $loop->last)<span aria-hidden="true">|</span>@endif @endforeach</span></p>@endif
                @if ($website->contact_email)<a class="footer__email" href="mailto:{{ $website->contact_email }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg><span>{{ $website->contact_email }}</span></a>@endif
                <p><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/></svg><span>{{ parse_url(url('/'), PHP_URL_HOST) }}</span></p>
            </div>
        </section>

        </div>
    </div>

    <div class="footer__bottom"><div class="container footer__bottom-content"><span>© {{ $currentYear }} {{ rtrim($website->company_name ?: $website->site_name, '.') }}. All rights reserved.</span><nav class="footer__legal" aria-label="{{ __('Pháp lý') }}">@foreach ($footerLegal as $legal)<a href="{{ $legal['url'] }}">{{ $legal['label'] }}</a>@endforeach<a href="{{ url('/sitemap.xml') }}">{{ __('Sơ đồ trang') }}</a></nav></div></div>
</footer>
