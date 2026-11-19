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

    public function test_portfolio_shows_case_study_and_filter_tabs_with_counts(): void
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
            ->assertSee('id="case-study"', false)
            ->assertSee('Tóm tắt nổi bật QA')
            ->assertSee('Tất cả (3)')
            ->assertSee('Nhóm thứ nhất QA (2)')
            ->assertSee('Nhóm thứ hai QA (1)')
            ->assertDontSee('Nhóm trống QA')
            ->assertDontSee('Nhóm con QA (');

        // Dự án của danh mục con thuộc nhóm lọc của danh mục gốc.
        $this->assertMatchesRegularExpression('/data-group="'.$first->id.'".*Dự án nhóm con QA/s', $response->getContent());
    }

    public function test_page_without_projects_has_no_case_study(): void
    {
        $this->get(route('home'))->assertOk()
            ->assertDontSee('id="case-study"', false)
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

    public function test_detail_uses_the_shared_review_block_and_comment_form(): void
    {
        $project = $this->project('Dự án đánh giá QA');

        $this->get(route('slug.show', ['slug' => $project->slug]))->assertOk()
            ->assertSee('Đánh giá về dự án')
            ->assertSee('review-layout', false)
            ->assertSee('star-rating__star', false)
            ->assertDontSee('d-inline-flex align-items-center fw-semibold gap-1', false)
            ->assertSee('action="'.route('comments.store', ['type' => $project->getMorphClass(), 'id' => $project->id]).'"', false)
            ->assertDontSee('project-case__feedback', false);
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
