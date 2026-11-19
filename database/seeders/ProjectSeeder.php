<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Database\Seeder;

/** Dự án mẫu: thay bằng dự án thật và ảnh trong admin. */
final class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['CNETPOS – Giải pháp POS & CNTT', 'Website Doanh nghiệp', 'Website giới thiệu dịch vụ, giải pháp thiết bị và phần mềm quản lý cho doanh nghiệp.', true],
            ['HGTrip – Du lịch & Lữ hành', 'Du lịch - Dịch vụ', 'Website tour, combo du lịch, đặt dịch vụ và quản lý lịch trình.', false],
            ['Xe Ghép Cẩm Khê – Dịch vụ vận chuyển', 'Landing Page', 'Website dịch vụ xe ghép, xe tiện chuyến Cẩm Khê – Yên Lập và các khu vực lân cận.', false],
            ['Nhật Quang Gia – Thiết bị gia dụng', 'Thương mại', 'Website giới thiệu và phân phối thiết bị điện gia dụng, điện lạnh chính hãng.', false],
            ['PingAnXing VN – Dịch vụ & Thương mại', 'Website Doanh nghiệp', 'Website giới thiệu dịch vụ, sản phẩm và giải pháp tổng thể cho doanh nghiệp.', false],
            ['Hệ thống quản lý bán hàng', 'Phần mềm', 'Phần mềm quản lý bán hàng, kho và báo cáo doanh thu theo thời gian thực.', false],
        ] as $index => [$title, $category, $excerpt, $featured]) {
            Project::query()->firstOrCreate(['title' => $title], [
                'project_category_id' => ProjectCategory::query()->where('name', $category)->value('id'),
                'excerpt' => $excerpt, 'body' => '<p>'.$excerpt.'</p>', 'status' => 'published', 'is_featured' => $featured,
                'sort_order' => ($index + 1) * 10, 'published_at' => now()->subDays($index), 'completed_at' => now()->toDateString(),
                'seo_title' => $title, 'seo_description' => $excerpt,
            ]);
        }
    }
}
