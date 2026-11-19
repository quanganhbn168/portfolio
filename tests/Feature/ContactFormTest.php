<?php

namespace Tests\Feature;

use App\Models\ContactRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Route liên hệ giới hạn 5 lượt/phút; các test gửi nhiều lần liên tiếp nên tắt giới hạn này.
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    private function payload(array $overrides = []): array
    {
        return $overrides + [
            'name' => 'Nguyễn Văn QA',
            'phone' => '0901234567',
            'message' => 'Cần tư vấn hệ thống PCCC cho nhà xưởng QA.',
        ];
    }

    public function test_name_phone_and_message_are_required(): void
    {
        foreach (['name', 'phone', 'message'] as $field) {
            $response = $this->from(route('contact'))->post(route('contact.store'), $this->payload([$field => '']));

            $response->assertSessionHasErrors($field);
        }

        $this->assertSame(0, ContactRequest::query()->count());
    }

    public function test_company_is_optional_on_the_contact_page_form(): void
    {
        $this->from(route('contact'))
            ->post(route('contact.store'), $this->payload(['company' => '']))
            ->assertSessionHasNoErrors();
    }

    public function test_error_messages_use_vietnamese_field_names(): void
    {
        $this->from(route('contact'))
            ->post(route('contact.store'), $this->payload(['phone' => '']))
            ->assertSessionHasErrors(['phone' => 'Vui lòng nhập số điện thoại.']);
    }

    public function test_valid_request_is_stored_without_a_timeline(): void
    {
        $this->from(route('contact'))->post(route('contact.store'), $this->payload())->assertSessionHasNoErrors();

        $request = ContactRequest::query()->firstOrFail();

        $this->assertSame('0901234567', $request->phone);
        $this->assertArrayNotHasKey('timeline', $request->getAttributes());
    }

    public function test_contact_page_marks_required_fields_and_has_no_timeline_field(): void
    {
        $this->get(route('contact'))->assertOk()
            ->assertDontSee('name="timeline"', false)
            ->assertDontSee('Thời gian dự kiến')
            ->assertSee('name="phone"', false)
            ->assertSee('Nội dung');
    }

    public function test_invalid_phone_numbers_are_rejected(): void
    {
        foreach (['abc', '12345', '0123456789', '09012345', '0901234567890', '+12', '0901-abc-567'] as $phone) {
            $this->from(route('contact'))
                ->post(route('contact.store'), $this->payload(['phone' => $phone]))
                ->assertSessionHasErrors('phone');
        }

        $this->assertSame(0, ContactRequest::query()->count());
    }

    public function test_common_phone_formats_are_accepted_and_normalised(): void
    {
        $cases = [
            '0901234567' => '0901234567',
            '0901 234 567' => '0901234567',
            '090.123.4567' => '0901234567',
            '(090) 123-4567' => '0901234567',
            '+84 901 234 567' => '0901234567',
            '84901234567' => '0901234567',
            '024 3825 1234' => '02438251234',
            '1900 6868' => '19006868',
            '+60 12 345 6789' => '+60123456789',
            '+1 (202) 555-0100' => '+12025550100',
        ];

        foreach ($cases as $input => $expected) {
            $this->from(route('contact'))
                ->post(route('contact.store'), $this->payload(['phone' => (string) $input]))
                ->assertSessionHasNoErrors();
        }

        $this->assertEqualsCanonicalizing(array_values($cases), ContactRequest::query()->pluck('phone')->all());
    }
}
