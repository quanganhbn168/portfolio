<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    private function makePost(string $title, array $attributes = []): Post
    {
        return Post::create($attributes + ['title' => $title, 'status' => 'published', 'body' => '<p>Nội dung QA</p>']);
    }

    public function test_blog_lists_only_published_posts(): void
    {
        $this->makePost('Bài đã đăng QA', ['excerpt' => 'Tóm tắt bài QA']);
        $this->makePost('Bài nháp QA', ['status' => 'draft']);
        $this->makePost('Bài hẹn giờ QA', ['published_at' => now()->addDay()]);

        $this->get(route('posts.index'))->assertOk()
            ->assertSee('Bài đã đăng QA')
            ->assertSee('Tóm tắt bài QA')
            ->assertDontSee('Bài nháp QA')
            ->assertDontSee('Bài hẹn giờ QA');
    }

    public function test_post_detail_renders_content_and_breadcrumb(): void
    {
        $post = $this->makePost('Chi tiết bài QA', ['body' => '<h2>Tiêu đề phụ QA</h2><p>Nội dung QA</p>']);

        $this->get(route('slug.show', ['slug' => $post->slug]))->assertOk()
            ->assertSee('Chi tiết bài QA')
            ->assertSee('Tiêu đề phụ QA')
            ->assertSee('article-prose', false)
            ->assertSee(route('posts.index'), false);
    }

    public function test_draft_post_returns_404(): void
    {
        $post = $this->makePost('Bài nháp ẩn QA', ['status' => 'draft']);

        $this->get(route('slug.show', ['slug' => $post->slug]))->assertNotFound();
    }

    public function test_home_shows_latest_posts_block(): void
    {
        $this->makePost('Bài mới trang chủ QA');

        $this->get(route('home'))->assertOk()
            ->assertSee('id="bai-viet"', false)
            ->assertSee('Bài mới trang chủ QA');
    }

    public function test_reading_time_is_at_least_one_minute(): void
    {
        $this->assertSame(1, $this->makePost('Ngắn QA')->reading_minutes);
    }
}
