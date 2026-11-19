<?php

namespace Database\Seeders;

use App\Models\Post;
use Database\Seeders\Concerns\ImportsSeedMedia;
use Illuminate\Database\Seeder;

/** Bài viết mẫu cho blog: thay bằng bài thật trong admin → Blog. */
final class PostSeeder extends Seeder
{
    use ImportsSeedMedia;

    public function run(): void
    {
        foreach ([
            ['post-laravel', 'Bắt đầu một dự án Laravel đúng cách', 'Những bước mình luôn làm đầu tiên khi nhận một dự án Laravel mới: cấu trúc, môi trường, migration và quy ước đặt tên.', 3],
            ['post-ai', 'Dùng Claude và ChatGPT để làm web nhanh hơn', 'Cách mình kết hợp các công cụ AI vào quy trình viết code, review và viết tài liệu mà vẫn kiểm soát được chất lượng.', 2],
            ['post-handover', 'Checklist bàn giao website cho khách hàng', 'Danh sách việc cần kiểm tra trước khi bàn giao: SEO, tốc độ, bảo mật, sao lưu và hướng dẫn quản trị.', 1],
        ] as [$image, $title, $excerpt, $daysAgo]) {
            $mediaId = $this->media($image, $title, 1200, 630);

            Post::query()->updateOrCreate(['title' => $title], [
                'curator_media_id' => $mediaId, 'seo_image_media_id' => $mediaId,
                'excerpt' => $excerpt, 'body' => $this->body($excerpt), 'status' => 'published',
                'published_at' => now()->subDays($daysAgo), 'seo_title' => $title, 'seo_description' => $excerpt,
            ]);
        }
    }

    private function body(string $excerpt): string
    {
        return <<<HTML
<p>{$excerpt} Đây là bài viết mẫu để anh xem định dạng blog; thay bằng bài thật trong admin.</p>
<h2>Vì sao quan trọng?</h2>
<p>Làm đúng từ đầu giúp <strong>tiết kiệm rất nhiều thời gian</strong> về sau, nhất là khi dự án lớn dần và có thêm người cùng tham gia.</p>
<ul><li>Cấu trúc rõ ràng, dễ bảo trì</li><li>Quy ước thống nhất giữa các dự án</li><li>Dễ bàn giao và mở rộng</li></ul>
<blockquote><p>Code chạy được mới là bước đầu. Code dễ đọc, dễ sửa mới là thứ khách hàng trả tiền cho.</p></blockquote>
<h3>Ví dụ lệnh</h3>
<pre><code>composer create-project laravel/laravel my-app
php artisan migrate --seed</code></pre>
<p>Cảm ơn anh đã đọc. Có góp ý gì cứ <a href="/contact">liên hệ</a> với mình nhé.</p>
HTML;
    }
}
