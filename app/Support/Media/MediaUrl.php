<?php

namespace App\Support\Media;

use App\Settings\WebsiteSettings;
use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;

class MediaUrl
{
    public static function resolve(?Media $media): ?string
    {
        return $media?->url;
    }

    public static function versioned(?Media $media): ?string
    {
        if (! $media?->url) {
            return null;
        }

        $separator = str_contains($media->url, '?') ? '&' : '?';
        $version = $media->updated_at?->getTimestamp() ?? $media->getKey();

        return "{$media->url}{$separator}v={$version}";
    }

    /**
     * Các bản WebP theo từng chiều rộng cho thuộc tính srcset/src.
     * Trả về null nếu ảnh không resize được (svg, gif...) để nơi gọi dùng ảnh gốc.
     * Không bao giờ phóng to ảnh lên quá kích thước gốc, và ảnh gốc không bị thay đổi.
     *
     * @param  list<int>  $widths
     * @return array{src: string, srcset: string}|null
     */
    public static function responsive(Media $media, array $widths = [480, 768, 1200], int $quality = 90): ?array
    {
        if (! $media->path || ! Curator::isResizable((string) $media->ext)) {
            return null;
        }

        $original = (int) $media->width;
        $widths = collect($widths)
            ->map(fn (int $width): int => $original > 0 ? min($width, $original) : $width)
            ->unique()
            ->sort()
            ->values();

        if ($widths->isEmpty()) {
            return null;
        }

        $glide = app(GlideManager::class);
        $urls = $widths->mapWithKeys(fn (int $width): array => [
            $width => $glide->getUrl($media->path, ['w' => $width, 'fm' => 'webp', 'q' => $quality]),
        ]);

        $src = $urls->get($widths->first(fn (int $width): bool => $width >= 768)) ?? $urls->last();

        return [
            'src' => $src,
            'srcset' => $urls->map(fn (string $url, int $width): string => "{$url} {$width}w")->implode(', '),
        ];
    }

    /**
     * Một bản WebP đúng một chiều rộng (Glide tạo và cache khi có người xem), dùng cho ảnh nền, banner,
     * thumbnail hoặc ảnh do JS đổi src. Không phóng to quá ảnh gốc; ảnh không resize được (svg, gif) trả về ảnh gốc.
     * Ảnh gốc vẫn được giữ nguyên để làm ảnh chia sẻ mạng xã hội (og:image).
     */
    public static function webp(?Media $media, int $width = 1600, int $quality = 82): ?string
    {
        if (! $media?->path || ! Curator::isResizable((string) $media->ext)) {
            return self::versioned($media);
        }

        $width = $media->width ? min($width, (int) $media->width) : $width;

        return app(GlideManager::class)->getUrl($media->path, ['w' => $width, 'fm' => 'webp', 'q' => $quality]);
    }

    /** Banner tràn màn hình: WebP rộng tối đa 1920px. */
    public static function banner(?Media $media): ?string
    {
        return self::webp($media, 1920, 80);
    }

    /** Banner chung trong cài đặt website, dùng khi trang/danh mục chưa có banner riêng. */
    public static function defaultBanner(): ?string
    {
        $mediaId = app(WebsiteSettings::class)->banner_media_id;

        return $mediaId ? self::banner(Media::query()->find($mediaId)) : null;
    }

    public static function mimeType(?Media $media): string
    {
        return match (strtolower((string) $media?->ext)) {
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'image/png',
        };
    }

    public static function iconSizes(?Media $media): string
    {
        if (! $media?->width || ! $media->height) {
            return 'any';
        }

        return "{$media->width}x{$media->height}";
    }
}
