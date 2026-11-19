<?php

namespace App\Support\Pages;

use App\Models\Page;
use App\Support\Media\MediaUrl;
use LogicException;

final class SystemPageProfileResolver
{
    private const REQUIRED_FIELDS = ['title', 'seo_title', 'seo_description'];

    /** @return array<string, mixed> */
    public function require(string $key): array
    {
        if (! array_key_exists($key, Page::SYSTEM_PAGES)) {
            throw new LogicException("Trang hệ thống [{$key}] không tồn tại.");
        }

        $page = Page::query()->system()->where('system_key', $key)->with(['seoImageMedia', 'bannerMedia'])->first();

        if (! $page) {
            throw new LogicException("Thiếu trang hệ thống [{$key}] trong mục Trang.");
        }

        foreach (self::REQUIRED_FIELDS as $field) {
            if (blank($page->{$field})) {
                throw new LogicException("Trang hệ thống [{$key}] thiếu {$field}.");
            }
        }

        $ogImage = $page->seoImageMedia;

        // Ảnh chia sẻ là tuỳ chọn (site mới chưa có ảnh nào); nhưng nếu đã chọn thì phải là ảnh.
        if ($page->seo_image_media_id !== null && (! $ogImage || ! str_starts_with((string) $ogImage->type, 'image/'))) {
            throw new LogicException("Ảnh chia sẻ (Open Graph) của trang hệ thống [{$key}] không hợp lệ.");
        }

        $banner = $page->bannerMedia;

        if ($page->banner_media_id !== null && (! $banner || ! str_starts_with((string) $banner->type, 'image/'))) {
            throw new LogicException("Banner của trang hệ thống [{$key}] không hợp lệ.");
        }

        return [
            'key' => $key,
            'id' => $page->getKey(),
            'title' => $page->title,
            'excerpt' => $page->excerpt,
            'seo_title' => $page->seo_title,
            'seo_description' => $page->seo_description,
            'og_image_url' => MediaUrl::versioned($ogImage) ?: url('images/no-image.png'),
            // banner_url có phương án dự phòng về banner chung; own_banner_url chỉ là banner đã chọn riêng cho trang.
            'banner_url' => MediaUrl::banner($banner) ?: MediaUrl::defaultBanner(),
            'own_banner_url' => MediaUrl::banner($banner),
        ];
    }
}
