<?php

namespace Database\Seeders;

use App\Settings\WebsiteSettings;
use Illuminate\Database\Seeder;

final class WebsiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = new WebsiteSettings([
            'site_name' => 'Anh Trần Portfolio',
            'tagline' => 'Website, landing page và giải pháp phần mềm',
            'footer_description' => 'Tổng hợp những website, landing page, hệ thống phần mềm và giải pháp số đã triển khai cho khách hàng.',
            'company_name' => '',
            'contact_email' => '',
            'hotline' => '',
            'contact_phone' => '',
            'address' => '',
            'facebook_url' => '',
            'zalo_url' => '',
            'youtube_url' => '',
            'services_url' => 'https://webappbacninh.vn',
            'services_label' => 'Bảng giá & dịch vụ',
            'seo_title' => 'Portfolio | Các dự án tiêu biểu đã triển khai',
            'seo_description' => 'Các dự án website, landing page, hệ thống phần mềm và giải pháp số tiêu biểu đã triển khai cho khách hàng.',
            'seo_keywords' => 'portfolio, thiết kế website, landing page, phần mềm',
            'logo_media_id' => null,
            'favicon_media_id' => null,
            'seo_image_media_id' => null,
            'company_profile_media_id' => null,
            'about_image_media_id' => null,
            'banner_media_id' => null,
            'footer_background_media_id' => null,
            'google_maps_embed_url' => null,
            'google_maps_url' => null,
            'phones' => [],
            'branches' => [],
        ]);
        $settings->settingsConfig()->resetDefaultValueLoadedProperties();
        $settings->save();
    }
}
