<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_is_the_portfolio_page(): void
    {
        Project::create(['title' => 'Dự án trang chủ QA', 'status' => 'published']);

        $this->get(route('home'))->assertOk()
            ->assertSee('pf-hero', false)
            ->assertSee('Dự án trang chủ QA');
    }

    public function test_old_projects_url_redirects_home(): void
    {
        $this->get('/projects')->assertRedirect('/');
    }
}
