<?php

namespace Tests\Feature;

use App\Mail\NewContactRequest;
use App\Models\ContactRequest;
use App\Settings\WebsiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactMailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ThrottleRequests::class);
        Mail::fake();

        $website = app(WebsiteSettings::class);
        $website->contact_email = 'nhan@example.test';
        $website->save();
    }

    private function payload(array $overrides = []): array
    {
        return $overrides + [
            'name' => 'Nguyễn Văn QA',
            'phone' => '0901234567',
            'message' => 'Cần tư vấn hệ thống PCCC cho nhà xưởng QA.',
        ];
    }

    public function test_contact_form_emails_the_configured_address(): void
    {
        // Form ở trang liên hệ.
        $this->from(route('contact'))
            ->post(route('contact.store'), $this->payload(['name' => 'Khách trang liên hệ']))
            ->assertSessionHasNoErrors();

        Mail::assertSent(NewContactRequest::class, 1);
        Mail::assertSent(
            NewContactRequest::class,
            fn (NewContactRequest $mail): bool => $mail->hasTo('nhan@example.test')
                && $mail->contactRequest->name === 'Khách trang liên hệ',
        );
    }

    public function test_no_email_is_sent_when_the_recipient_is_missing_but_the_request_is_still_saved(): void
    {
        $website = app(WebsiteSettings::class);
        $website->contact_email = '';
        $website->save();

        $this->from(route('contact'))->post(route('contact.store'), $this->payload())->assertSessionHasNoErrors();

        Mail::assertNothingSent();
        $this->assertSame(1, ContactRequest::query()->count());
    }

    public function test_honeypot_submissions_send_no_email_and_store_nothing(): void
    {
        $this->from(route('contact'))
            ->post(route('contact.store'), $this->payload(['website_url' => 'http://spam.test']));

        Mail::assertNothingSent();
        $this->assertSame(0, ContactRequest::query()->count());
    }
}
