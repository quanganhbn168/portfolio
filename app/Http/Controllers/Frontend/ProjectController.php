<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PricingPlan;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Support\Media\MediaUrl;
use App\Support\Pages\SystemPageProfileResolver;
use App\Support\Projects\ProjectDetailContent;
use App\Support\Seo\FrontendSeoBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly SystemPageProfileResolver $systemPages,
    ) {}

    public function index(Request $request): View
    {
        $page = $this->systemPages->require('home');
        $data = $this->listingData(request: $request);
        $data['page'] = $page;
        $data['pageTitle'] = $page['title'];
        $data['pageBannerUrl'] = $page['banner_url'];
        $data['pageDescription'] = filled($page['excerpt']) ? $page['excerpt'] : $data['pageDescription'];
        $data['seo'] = $this->seo->systemPage($page, 'home');

        return view('frontend.projects.index', $data);
    }

    public function category(ProjectCategory $category, ?Request $request = null): View
    {
        abort_unless($category->is_active, 404);
        $category->loadMissing('slugs');

        $title = $category->seo_title ?: $category->name.' | '.__('Dự án');
        $description = $category->seo_description ?: $category->description ?: __('Các dự án thuộc nhóm :name.', ['name' => $category->name]);

        $request ??= request();

        return view('frontend.projects.index', $this->listingData($category, request: $request) + [
            'seo' => $this->seo->listing($title, $description, route('slug.show', ['slug' => $category->slug]), image: $category->seoImageUrl()),
        ]);
    }

    public function show(Project $project): View
    {
        abort_unless($project->status === 'published' && (! $project->published_at || $project->published_at->isPast()), 404);

        $project->load([
            'category.slugs',
            'curatorMedia',
            'slugs',
        ]);
        $content = ProjectDetailContent::make($project);
        $relatedProjects = Project::query()
            ->published()
            ->whereKeyNot($project->id)
            ->with(['category', 'curatorMedia', 'slugs'])
            ->orderByRaw('project_category_id = ? desc', [$project->project_category_id])
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('frontend.projects.show', compact('project') + [
            'content' => $content,
            'relatedProjects' => $relatedProjects,
            'projectVideoUrl' => filter_var($project->video_url, FILTER_VALIDATE_URL) ? $project->video_url : null,
            'seo' => $this->seo->project($project),
        ]);
    }

    /** @return array<string, mixed> */
    private function listingData(
        ?ProjectCategory $activeCategory = null,
        ?Request $request = null,
    ): array {
        $request ??= request();
        $sort = $request->string('sort')->value();
        $sort = in_array($sort, ['latest', 'featured', 'title'], true) ? $sort : 'latest';

        $projects = null;
        $portfolio = [];

        if ($activeCategory) {
            $projectsQuery = Project::query()
                ->published()
                ->whereIn('project_category_id', $activeCategory->subtreeIds(activeOnly: true))
                ->with(['category', 'curatorMedia', 'slugs']);
            $this->applyOrdering($projectsQuery, $sort);
            $projects = $projectsQuery->paginate(12)->withQueryString();
        } else {
            $portfolio = $this->portfolio();
        }

        return [
            'activeCategory' => $activeCategory,
            'pageBannerUrl' => $activeCategory?->banner_url ?: MediaUrl::defaultBanner(),
            'categoryImageUrl' => $activeCategory?->image_url,
            'categoryBodyHtml' => (string) str((string) $activeCategory?->body)->sanitizeHtml(),
            'projects' => $projects,
            'pageTitle' => $activeCategory?->name ?? __('Dự án'),
            'pageDescription' => $activeCategory?->description ?: __('Những công trình đã được triển khai.'),
        ] + $portfolio;
    }

    /**
     * Dữ liệu trang portfolio: các dự án đã xuất bản (lọc phía trình duyệt theo danh mục gốc),
     * và nhóm lọc.
     *
     * @return array<string, mixed>
     */
    private function portfolio(): array
    {
        $projects = Project::query()
            ->published()
            ->with(['category.parent', 'curatorMedia', 'slugs'])
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();

        // Nhóm lọc của một dự án = danh mục gốc của nó (0 nếu chưa gắn danh mục).
        $groupOf = fn (Project $project): ?ProjectCategory => $project->category?->parent ?? $project->category;
        $projects->each(fn (Project $project) => $project->setAttribute('filter_group', $groupOf($project)?->id ?? 0));

        $filters = $projects
            ->groupBy('filter_group')
            ->map(fn (Collection $items, int $id): array => [
                'id' => $id,
                'name' => $id === 0 ? __('Dự án khác') : $groupOf($items->first())->name,
                'sort' => $id === 0 ? PHP_INT_MAX : (int) $groupOf($items->first())->sort_order,
                'count' => $items->count(),
            ])
            ->sortBy('sort')
            ->values();

        return [
            'portfolioProjects' => $projects,
            'portfolioFilters' => $filters,
            'services' => Service::query()->active()->get(),
            'plans' => PricingPlan::query()->active()->get(),
            'latestPosts' => Post::query()->published()->with(['curatorMedia', 'slugs'])->orderByDesc('published_at')->limit(3)->get(),
        ];
    }

    private function applyOrdering(Builder $query, string $sort): void
    {
        match ($sort) {
            'featured' => $query->orderByDesc('is_featured')->orderByDesc('published_at'),
            'title' => $query->orderBy('title'),
            default => $query->orderByDesc('published_at')->orderByDesc('id'),
        };
    }
}
