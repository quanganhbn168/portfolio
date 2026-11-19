{{-- Mạng xã hội cấu hình trong admin; mục nào bỏ trống thì không hiện. Logo ở public/images/social. --}}
@php
    $socialLinks = collect([
        ['Facebook', 'facebook', $website->facebook_url],
        ['Zalo', 'zalo', $website->zalo_url],
        ['YouTube', 'youtube', $website->youtube_url],
        ['GitHub', 'github', $website->github_url],
        ['TikTok', 'tiktok', $website->tiktok_url],
        ['Telegram', 'telegram', $website->telegram_url],
        ['Email', 'gmail', filled($website->contact_email) ? 'mailto:'.$website->contact_email : null],
    ])->filter(fn (array $link): bool => filled($link[2]));
@endphp
@if ($socialLinks->isNotEmpty())
    <div class="footer__socials {{ $class ?? '' }}">
        @foreach ($socialLinks as [$label, $icon, $url])
            <a class="footer__social" href="{{ $url }}" @unless (str_starts_with($url, 'mailto:')) target="_blank" rel="noopener noreferrer" @endunless aria-label="{{ $label }}" title="{{ $label }}"><img src="{{ asset('images/social/'.$icon.'.svg') }}" alt="" width="20" height="20" loading="lazy"></a>
        @endforeach
    </div>
@endif
