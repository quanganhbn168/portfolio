<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/** Các trang hệ thống cố định: mỗi trang một bản ghi trong bảng pages, sửa trong mục Trang của admin. */
final class SystemPageSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'home' => ['Các dự án tiêu biểu đã triển khai', 'Portfolio | Các dự án tiêu biểu đã triển khai', 'Tổng hợp website, landing page, hệ thống phần mềm và giải pháp số đã triển khai cho khách hàng.'],
            'about' => ['Giới thiệu', 'Giới thiệu', 'Giới thiệu về năng lực, kinh nghiệm và cách làm việc.'],
            'contact' => ['Liên hệ', 'Liên hệ tư vấn dự án', 'Liên hệ để được tư vấn miễn phí cho dự án của bạn.'],
        ] as $key => [$title, $seoTitle, $seoDescription]) {
            Page::query()->updateOrCreate(['system_key' => $key], [
                'type' => Page::TYPE_SYSTEM,
                'title' => $title,
                'status' => 'published',
                'sort_order' => (array_search($key, array_keys(Page::SYSTEM_PAGES), true) + 1) * 10,
                'excerpt' => $seoDescription,
                'seo_title' => $seoTitle,
                'seo_description' => $seoDescription,
            ]);
        }
    }
}
