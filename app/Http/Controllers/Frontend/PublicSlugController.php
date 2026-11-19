<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PageController;
use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Slug;
use Illuminate\Http\Request;

class PublicSlugController extends Controller
{
    /**
     * Hiển thị trực tiếp mọi trang công khai (chi tiết và danh mục) theo bảng slug dùng chung, không chuyển hướng.
     */
    public function __invoke(Request $request, string $slug)
    {
        return $this->resolve($request, $slug);
    }

    private function resolve(Request $request, string $slug)
    {
        $sluggable = Slug::query()
            ->where('slug', $slug)
            ->with('sluggable')
            ->first()?->sluggable;

        $sluggable?->loadMissing('slugs');

        // Trang chi tiết/danh mục dùng chung route slug.show, nên báo cho menu biết mục lục nào đang đứng
        // để mục "Năng lực", "Dự án"... sáng lên cả khi khách đang ở trang con.
        $request->attributes->set('frontend.menu_route', match (true) {
            $sluggable instanceof Project, $sluggable instanceof ProjectCategory => 'projects.index',
            $sluggable instanceof Post => 'posts.index',
            default => null,
        });

        return match (true) {
            $sluggable instanceof Page => app(PageController::class)->show($sluggable),
            $sluggable instanceof Project => app(ProjectController::class)->show($sluggable),
            $sluggable instanceof ProjectCategory => app(ProjectController::class)->category($sluggable, $request),
            $sluggable instanceof Post => app(PostController::class)->show($sluggable),
            default => abort(404),
        };
    }
}
