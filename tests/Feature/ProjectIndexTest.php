<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectIndexTest extends TestCase
{
    use RefreshDatabase;

    private function project(string $title, array $attributes = []): Project
    {
        return Project::create($attributes + ['title' => $title, 'status' => 'published']);
    }

    public function test_portfolio_shows_filter_tabs_with_count_badges(): void
    {
        $first = ProjectCategory::create(['name' => 'Nhóm thứ nhất QA', 'is_active' => true, 'sort_order' => 10]);
        $second = ProjectCategory::create(['name' => 'Nhóm thứ hai QA', 'is_active' => true, 'sort_order' => 20]);
        ProjectCategory::create(['name' => 'Nhóm trống QA', 'is_active' => true, 'sort_order' => 30]);
        $child = ProjectCategory::create(['name' => 'Nhóm con QA', 'is_active' => true, 'parent_id' => $first->id]);

        $this->project('Dự án nổi bật QA', ['is_featured' => true, 'excerpt' => 'Tóm tắt nổi bật QA', 'project_category_id' => $first->id]);
        $this->project('Dự án nhóm con QA', ['project_category_id' => $child->id]);
        $this->project('Dự án nhóm hai QA', ['project_category_id' => $second->id]);

        $response = $this->get(route('home'))->assertOk()
            ->assertSee('pf-hero', false)
            ->assertSee('id="cong-nghe"', false)
            ->assertSee('Tóm tắt nổi bật QA')
            ->assertSee('<span class="pf-tab__count">3</span>', false)
            ->assertSee('Nhóm thứ nhất QA')
            ->assertSee('<span class="pf-tab__count">2</span>', false)
            ->assertSee('Nhóm thứ hai QA')
            ->assertDontSee('Nhóm trống QA')
            ->assertDontSee('data-filter="'.$child->id.'"', false);

        // Dự án của danh mục con thuộc nhóm lọc của danh mục gốc.
        $this->assertMatchesRegularExpression('/data-group="'.$first->id.'".*Dự án nhóm con QA/s', $response->getContent());
    }

    public function test_page_without_projects_shows_empty_state(): void
    {
        $this->get(route('home'))->assertOk()
            ->assertSee('Chưa có dự án được xuất bản');
    }

    public function test_detail_breadcrumb_includes_the_project_category(): void
    {
        $category = ProjectCategory::create(['name' => 'Nhóm công trình QA', 'is_active' => true]);
        $project = $this->project('Dự án có nhóm QA', ['project_category_id' => $category->id]);

        $this->get(route('slug.show', ['slug' => $project->slug]))->assertOk()
            ->assertSeeInOrder([
                route('home'),
                route('slug.show', ['slug' => $category->slug]),
                'Nhóm công trình QA',
                'aria-current="page"',
            ], false);
    }

    public function test_category_page_is_a_plain_grid_without_the_portfolio_hero(): void
    {
        $category = ProjectCategory::create(['name' => 'Nhóm lưới QA', 'is_active' => true]);
        $this->project('Dự án trong nhóm A QA', ['project_category_id' => $category->id, 'is_featured' => true]);
        $this->project('Dự án trong nhóm B QA', ['project_category_id' => $category->id]);

        $this->get(route('slug.show', ['slug' => $category->slug]))->assertOk()
            ->assertDontSee('pf-hero', false)
            ->assertSee('project-tile', false)
            ->assertSee('Dự án trong nhóm A QA')
            ->assertSee('Dự án trong nhóm B QA');
    }
}
