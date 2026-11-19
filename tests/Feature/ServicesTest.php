<?php

namespace Tests\Feature;

use App\Models\PricingPlan;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_page_lists_active_services_and_pricing(): void
    {
        Service::create(['title' => 'Dịch vụ hiện QA', 'description' => 'Mô tả dịch vụ QA']);
        Service::create(['title' => 'Dịch vụ ẩn QA', 'is_active' => false]);
        PricingPlan::create(['name' => 'Gói nổi bật QA', 'price' => '9.999.000đ', 'features' => ['Quyền lợi một QA', 'Quyền lợi hai QA'], 'is_featured' => true]);

        $this->get(route('services.index'))->assertOk()
            ->assertSee('Dịch vụ hiện QA')
            ->assertSee('Mô tả dịch vụ QA')
            ->assertDontSee('Dịch vụ ẩn QA')
            ->assertSee('Gói nổi bật QA')
            ->assertSee('9.999.000đ')
            ->assertSee('Quyền lợi hai QA')
            ->assertSee('price-card--featured', false);
    }

    public function test_home_shows_services_and_pricing_blocks_without_external_links(): void
    {
        Service::create(['title' => 'Dịch vụ trang chủ QA']);
        PricingPlan::create(['name' => 'Gói trang chủ QA', 'price' => 'Liên hệ']);

        $this->get(route('home'))->assertOk()
            ->assertSee('id="dich-vu"', false)
            ->assertSee('id="bang-gia"', false)
            ->assertSee('Dịch vụ trang chủ QA')
            ->assertSee('Gói trang chủ QA')
            ->assertDontSee('webappbacninh', false);
    }
}
