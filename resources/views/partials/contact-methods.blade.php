{{-- Các cách liên lạc: chỉ hiện mục nào đã nhập trong Cài đặt website. Logo ở public/images/social. --}}
@php
    $short = fn (string $url): string => rtrim((string) preg_replace('#^https?://(www\.)?#', '', $url), '/');
    $methods = collect();

    foreach ($footerContactPhones as $phone) {
        $methods->push(['label' => __('Điện thoại'), 'value' => $phone['label'], 'url' => $phone['href'], 'fa' => 'fa-phone']);
    }
    if (filled($website->contact_email)) {
        $methods->push(['label' => 'Email', 'value' => $website->contact_email, 'url' => 'mailto:'.$website->contact_email, 'img' => 'gmail']);
    }
    if (filled($website->zalo_url)) {
        $methods->push(['label' => 'Zalo', 'value' => $short($website->zalo_url), 'url' => $website->zalo_url, 'img' => 'zalo']);
    }
    if (filled($website->wechat)) {
        $methods->push(['label' => 'WeChat', 'value' => $website->wechat, 'url' => null, 'img' => 'wechat']);
    }
    if (filled($website->whatsapp)) {
        $methods->push(['label' => 'WhatsApp', 'value' => $website->whatsapp, 'url' => 'https://wa.me/'.preg_replace('/\D/', '', $website->whatsapp), 'img' => 'whatsapp']);
    }
    foreach ([
        ['Telegram', 'telegram', $website->telegram_url], ['Facebook', 'facebook', $website->facebook_url], ['LinkedIn', 'linkedin', $website->linkedin_url],
        ['GitHub', 'github', $website->github_url], ['TikTok', 'tiktok', $website->tiktok_url], ['YouTube', 'youtube', $website->youtube_url],
    ] as [$label, $icon, $url]) {
        if (filled($url)) {
            $methods->push(['label' => $label, 'value' => $short($url), 'url' => $url, 'img' => $icon]);
        }
    }
    if (filled($website->address)) {
        $methods->push(['label' => __('Địa chỉ'), 'value' => $website->address, 'url' => null, 'fa' => 'fa-location-dot']);
    }
@endphp
@if ($methods->isNotEmpty())
    <ul class="contact-methods {{ ($compact ?? false) ? 'contact-methods--compact' : '' }}">
        @foreach ($methods as $method)
            <li>
                @if ($method['url'])<a class="contact-method" href="{{ $method['url'] }}" @unless (str_starts_with($method['url'], 'tel:') || str_starts_with($method['url'], 'mailto:')) target="_blank" rel="noopener noreferrer" @endunless>@else<div class="contact-method">@endif
                    <span class="contact-method__logo">
                        @isset($method['img'])<img src="{{ asset('images/social/'.$method['img'].'.svg') }}" alt="" width="24" height="24" loading="lazy">@else<i class="fa-solid {{ $method['fa'] }}" aria-hidden="true"></i>@endisset
                    </span>
                    <span class="contact-method__text"><small>{{ $method['label'] }}</small><strong>{{ $method['value'] }}</strong></span>
                @if ($method['url'])</a>@else</div>@endif
            </li>
        @endforeach
    </ul>
@endif
