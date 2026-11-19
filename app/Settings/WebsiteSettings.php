<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class WebsiteSettings extends Settings
{
    public string $site_name = '';

    public string $tagline = '';

    public string $footer_description = '';

    public string $company_name = '';

    public string $contact_email = '';

    public string $hotline = '';

    public string $contact_phone = '';

    public string $address = '';

    public string $facebook_url = '';

    public string $zalo_url = '';

    public string $youtube_url = '';

    /** Trang bảng giá & dịch vụ (website chính của doanh nghiệp). */
    public string $services_url = '';

    public string $services_label = '';

    /** Giới thiệu bản thân ("Tôi là ai"). */
    public string $owner_name = '';

    public string $owner_title = '';

    public string $owner_bio = '';

    public ?int $owner_media_id = null;

    public ?int $owner_cover_media_id = null;

    public string $owner_workplace = '';

    public string $owner_location = '';

    public string $github_url = '';

    public string $tiktok_url = '';

    public string $telegram_url = '';

    public string $seo_title = '';

    public string $seo_description = '';

    public string $seo_keywords = '';

    public ?int $logo_media_id = null;

    public ?int $favicon_media_id = null;

    public ?int $seo_image_media_id = null;

    public ?int $company_profile_media_id = null;

    public ?int $about_image_media_id = null;

    public ?int $banner_media_id = null;

    public ?int $footer_background_media_id = null;

    public ?string $google_maps_embed_url = null;

    public ?string $google_maps_url = null;

    public array $phones = [];

    public array $branches = [];

    /** Số điện thoại chính: số đánh dấu chính, số đầu tiên trong danh sách, hoặc hotline cũ. */
    public function primaryPhone(): ?string
    {
        $phones = collect($this->phones)
            ->filter(fn (mixed $phone): bool => is_array($phone) && filled($phone['number'] ?? null));

        return $phones->firstWhere('is_primary', true)['number']
            ?? $phones->first()['number']
            ?? (filled($this->hotline) ? $this->hotline : null);
    }

    public static function group(): string
    {
        return 'website';
    }
}
