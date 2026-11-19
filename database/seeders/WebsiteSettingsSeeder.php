<?php

namespace Database\Seeders;

use App\Settings\WebsiteSettings;
use Database\Seeders\Concerns\ImportsSeedMedia;
use Illuminate\Database\Seeder;

final class WebsiteSettingsSeeder extends Seeder
{
    use ImportsSeedMedia;

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
            'owner_name' => 'Anh Trần',
            'owner_title' => 'Lập trình viên Web · Founder WebApp Bắc Ninh',
            'owner_bio' => 'Xin chào, tôi là Anh. Tôi thiết kế, lập trình và vận hành website, landing page và phần mềm cho doanh nghiệp, từ khảo sát nhu cầu, thiết kế, lập trình đến bàn giao và bảo trì. Tôi làm việc chủ yếu với Laravel, MySQL, Tailwind CSS và dùng các công cụ AI như Claude, ChatGPT để làm nhanh hơn mà vẫn chắc tay. (Nội dung mẫu: sửa trong admin → Cài đặt website → Liên hệ → Tôi là ai.)',
            'owner_media_id' => $this->media('avatar', 'Ảnh đại diện', 400, 400),
            'owner_cover_media_id' => $this->media('cover', 'Ảnh bìa', 1600, 533),
            'owner_workplace' => 'WebApp Bắc Ninh',
            'owner_location' => 'Bắc Ninh, Việt Nam',
            'github_url' => 'https://github.com/quanganhbn168',
            'tiktok_url' => '',
            'telegram_url' => '',
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
