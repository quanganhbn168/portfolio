<?php

namespace Tests\Feature;

use App\Models\ContactRequest;
use App\Models\PricingPlan;
use App\Settings\WebsiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

class ContactSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_url_redirects_to_the_home_contact_section(): void
    {
        $this->get('/contact')->assertRedirect('/#lien-he');
    }

    public function test_home_shows_only_the_contact_methods_that_exist_in_settings(): void
    {
        $website = app(WebsiteSettings::class);
        $website->zalo_url = 'https://zalo.me/0901234567';
        $website->wechat = 'wechat-qa-id';
        $website->linkedin_url = 'https://www.linkedin.com/in/qa';
        $website->telegram_url = '';
        $website->save();

        $this->get(route('home'))->assertOk()
            ->assertSee('id="lien-he"', false)
            ->assertSee('images/social/zalo.svg', false)
            ->assertSee('wechat-qa-id')
            ->assertSee('images/social/wechat.svg', false)
            ->assertSee('images/social/linkedin.svg', false)
            ->assertDontSee('images/social/telegram.svg', false);
    }

    public function test_quote_modal_is_on_the_page_and_pricing_buttons_open_it(): void
    {
        PricingPlan::create(['name' => 'Gói modal QA', 'price' => '1đ']);

        $this->get(route('services.index'))->assertOk()
            ->assertSee('id="quote-modal"', false)
            ->assertSee('data-bs-target="#quote-modal"', false)
            ->assertSee('data-plan="Gói modal QA"', false);
    }

    public function test_contact_form_accepts_ajax_without_a_page_reload(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);

        $this->postJson(route('contact.store'), ['name' => 'Khách AJAX QA', 'phone' => '0901234567', 'message' => 'Cần báo giá QA'])
            ->assertOk()
            ->assertJsonStructure(['message']);

        $this->assertSame('Khách AJAX QA', ContactRequest::query()->value('name'));
    }

    public function test_ajax_validation_errors_come_back_as_json(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);

        $this->postJson(route('contact.store'), ['name' => '', 'phone' => '', 'message' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'phone', 'message']);
    }
}
