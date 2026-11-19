<?php

namespace App\Support\Media;

final class YoutubeUrl
{
    private const HOSTS = ['youtu.be', 'youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com'];

    /** Mã video YouTube lấy từ link (watch, youtu.be, embed, shorts, live); null nếu không phải link YouTube hợp lệ. */
    public static function id(?string $url): ?string
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if (! in_array($host, self::HOSTS, true)) {
            return null;
        }

        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        $id = $host === 'youtu.be'
            ? $path
            : ($query['v'] ?? (preg_match('~^(?:embed|shorts|live)/([\w-]{6,20})~', $path, $m) ? $m[1] : null));

        return is_string($id) && preg_match('~^[\w-]{6,20}$~', $id) ? $id : null;
    }

    public static function embedUrl(string $id): string
    {
        return 'https://www.youtube-nocookie.com/embed/'.$id.'?autoplay=1&rel=0';
    }

    public static function thumbnailUrl(string $id): string
    {
        return 'https://i.ytimg.com/vi/'.$id.'/hqdefault.jpg';
    }
}
