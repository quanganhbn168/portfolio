<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectCategory;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/** Dự án mẫu kèm ảnh mockup (database/seeders/images): thay bằng dự án thật và ảnh trong admin. */
final class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['cnetpos', 'CNETPOS – Giải pháp POS & CNTT', 'Website Doanh nghiệp', 'Website giới thiệu dịch vụ, giải pháp thiết bị và phần mềm quản lý cho doanh nghiệp.', true],
            ['hgtrip', 'HGTrip – Du lịch & Lữ hành', 'Du lịch - Dịch vụ', 'Website tour, combo du lịch, đặt dịch vụ và quản lý lịch trình.', false],
            ['xe-ghep-cam-khe', 'Xe Ghép Cẩm Khê – Dịch vụ vận chuyển', 'Landing Page', 'Website dịch vụ xe ghép, xe tiện chuyến Cẩm Khê – Yên Lập và các khu vực lân cận.', false],
            ['nhat-quang-gia', 'Nhật Quang Gia – Thiết bị gia dụng', 'Thương mại', 'Website giới thiệu và phân phối thiết bị điện gia dụng, điện lạnh chính hãng.', false],
            ['pinganxing', 'PingAnXing VN – Dịch vụ & Thương mại', 'Website Doanh nghiệp', 'Website giới thiệu dịch vụ, sản phẩm và giải pháp tổng thể cho doanh nghiệp.', false],
            ['quan-ly-ban-hang', 'Hệ thống quản lý bán hàng', 'Phần mềm', 'Phần mềm quản lý bán hàng, kho và báo cáo doanh thu theo thời gian thực.', false],
        ] as $index => [$image, $title, $category, $excerpt, $featured]) {
            $mediaId = $this->media($image, $title);

            Project::query()->updateOrCreate(['title' => $title], [
                'project_category_id' => ProjectCategory::query()->where('name', $category)->value('id'),
                'curator_media_id' => $mediaId, 'seo_image_media_id' => $mediaId,
                'excerpt' => $excerpt, 'body' => '<p>'.$excerpt.'</p>', 'status' => 'published', 'is_featured' => $featured,
                'sort_order' => ($index + 1) * 10, 'published_at' => now()->subDays($index), 'completed_at' => now()->toDateString(),
                'seo_title' => $title, 'seo_description' => $excerpt,
            ]);
        }
    }

    /** Chép ảnh mẫu vào disk public và tạo bản ghi trong thư viện media. */
    private function media(string $name, string $title): int
    {
        $filename = $name.'.svg';
        $source = database_path('seeders/images/'.$filename);
        $disk = Storage::disk('public');

        if (! $disk->exists($filename)) {
            $disk->put($filename, file_get_contents($source));
        }

        return Media::query()->firstOrCreate(['disk' => 'public', 'path' => $filename], [
            'directory' => null, 'visibility' => 'public', 'name' => $name,
            'width' => 1200, 'height' => 750, 'size' => filesize($source) ?: 0,
            'type' => 'image/svg+xml', 'ext' => 'svg', 'alt' => $title, 'title' => $title,
        ])->getKey();
    }
}
