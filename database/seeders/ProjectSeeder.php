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
    /** Video mẫu (placeholder) để xem phần video trên trang chi tiết. */
    private const SAMPLE_VIDEO = 'https://www.youtube.com/watch?v=aqz-KE-bpKQ';

    public function run(): void
    {
        // [ảnh, link thực tế, tên, danh mục, mô tả, giá, bắt đầu, hoàn thành, tình trạng, công nghệ, nổi bật]
        $projects = [
            ['cnetpos', 'https://cnetpos.example.com', 'CNETPOS – Giải pháp POS & CNTT', 'Website Doanh nghiệp', 'Website giới thiệu dịch vụ, giải pháp thiết bị và phần mềm quản lý cho doanh nghiệp.', '25.000.000đ', '2026-03-01', '2026-05-15', 'completed', 'Laravel, MySQL, Tailwind CSS', true],
            ['hgtrip', 'https://hgtrip.example.com', 'HGTrip – Du lịch & Lữ hành', 'Du lịch - Dịch vụ', 'Website tour, combo du lịch, đặt dịch vụ và quản lý lịch trình.', '32.000.000đ', '2026-06-01', null, 'in_progress', 'Laravel, Livewire, MySQL', false],
            ['xe-ghep-cam-khe', 'https://xe-ghep-cam-khe.example.com', 'Xe Ghép Cẩm Khê – Dịch vụ vận chuyển', 'Landing Page', 'Website dịch vụ xe ghép, xe tiện chuyến Cẩm Khê – Yên Lập và các khu vực lân cận.', '6.000.000đ', '2026-01-10', '2026-01-25', 'maintenance', 'Laravel, Bootstrap', false],
            ['nhat-quang-gia', 'https://nhat-quang-gia.example.com', 'Nhật Quang Gia – Thiết bị gia dụng', 'Thương mại', 'Website giới thiệu và phân phối thiết bị điện gia dụng, điện lạnh chính hãng.', '18.000.000đ', '2025-11-01', '2026-01-05', 'completed', 'Laravel, MySQL, Vite', false],
            ['pinganxing', 'https://pinganxing.example.com', 'PingAnXing VN – Dịch vụ & Thương mại', 'Website Doanh nghiệp', 'Website giới thiệu dịch vụ, sản phẩm và giải pháp tổng thể cho doanh nghiệp.', '22.000.000đ', '2025-09-01', '2025-10-20', 'completed', 'Laravel, Tailwind CSS', false],
            ['quan-ly-ban-hang', 'https://quan-ly-ban-hang.example.com', 'Hệ thống quản lý bán hàng', 'Phần mềm', 'Phần mềm quản lý bán hàng, kho và báo cáo doanh thu theo thời gian thực.', '60.000.000đ', '2026-07-01', null, 'planning', 'Laravel, Livewire, MySQL, Git', false],
        ];

        $gallery = collect($projects)->mapWithKeys(fn (array $p): array => [$p[0] => $this->media($p[0], $p[2])]);

        foreach ($projects as $index => [$image, $website, $title, $category, $excerpt, $price, $start, $done, $status, $tech, $featured]) {
            $others = $gallery->except($image)->take(3)->values()->all();

            Project::query()->updateOrCreate(['title' => $title], [
                'project_category_id' => ProjectCategory::query()->where('name', $category)->value('id'),
                'website_url' => $website, 'curator_media_id' => $gallery[$image], 'seo_image_media_id' => $gallery[$image],
                'gallery' => [$gallery[$image], ...$others],
                'client_name' => explode(' – ', $title)[0], 'industry' => $category,
                'price' => $price, 'started_at' => $start, 'completed_at' => $done, 'project_status' => $status, 'technologies' => $tech,
                'video_url' => $index === 0 ? self::SAMPLE_VIDEO : null,
                'excerpt' => $excerpt, 'body' => $this->body($title, $excerpt), 'status' => 'published', 'is_featured' => $featured,
                'sort_order' => ($index + 1) * 10, 'published_at' => now()->subDays($index),
                'seo_title' => $title, 'seo_description' => $excerpt,
            ]);
        }
    }

    /** Nội dung rich mẫu: tiêu đề, danh sách, trích dẫn, bảng, code… để xem CSS. */
    private function body(string $title, string $excerpt): string
    {
        return <<<HTML
<p>{$excerpt} Đây là nội dung mẫu để anh xem định dạng; thay bằng nội dung thật trong admin.</p>
<h2>Bài toán của khách hàng</h2>
<p>Khách hàng cần một website <strong>nhanh, dễ quản trị</strong> và có thể mở rộng. Một số yêu cầu chính:</p>
<ul><li>Giao diện responsive, tải nhanh trên điện thoại</li><li>Chuẩn SEO, cấu trúc rõ ràng</li><li>Trang quản trị để tự cập nhật nội dung</li></ul>
<blockquote><p>Làm website không chỉ là đẹp, mà phải giúp khách hàng bán được hàng và dễ vận hành.</p></blockquote>
<h2>Cách triển khai</h2>
<ol><li>Khảo sát nhu cầu và thống nhất phạm vi</li><li>Thiết kế giao diện và duyệt với khách</li><li>Lập trình, kiểm thử và bàn giao</li></ol>
<h3>Thông số kỹ thuật</h3>
<table><thead><tr><th>Hạng mục</th><th>Chi tiết</th></tr></thead><tbody><tr><td>Back-end</td><td>Laravel, MySQL</td></tr><tr><td>Front-end</td><td>Tailwind CSS, Vite</td></tr><tr><td>Quản lý mã nguồn</td><td>Git, GitHub</td></tr></tbody></table>
<h3>Ví dụ đoạn code</h3>
<pre><code>php artisan migrate --seed</code></pre>
<p>Xem thêm <a href="https://laravel.com" target="_blank" rel="noopener">tài liệu Laravel</a> nếu cần.</p>
HTML;
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
