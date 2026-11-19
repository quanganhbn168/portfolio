<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactRequests\ContactRequestResource;
use App\Filament\Resources\ContactRequests\Pages\ListContactRequests;
use App\Models\ContactRequest;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NavigationBadgeTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findByName('super_admin', 'web'));

        return $admin;
    }

    private function createContactRequest(string $status = 'new'): ContactRequest
    {
        return ContactRequest::create(['name' => 'A', 'phone' => '0901234567', 'message' => 'x', 'status' => $status]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('app:setup-admin')->assertSuccessful();
        Filament::setCurrentPanel('admin');
    }

    public function test_badge_is_hidden_when_there_is_nothing(): void
    {
        $this->actingAs($this->makeAdmin());

        $this->assertNull(ContactRequestResource::getNavigationBadge());
    }

    public function test_badge_counts_unseen_records_regardless_of_status(): void
    {
        $this->actingAs($this->makeAdmin());

        $this->createContactRequest('new');
        $this->createContactRequest('contacted');

        $this->assertSame('2', ContactRequestResource::getNavigationBadge());
        $this->assertSame('danger', ContactRequestResource::getNavigationBadgeColor());
    }

    public function test_opening_the_list_clears_the_badge_until_new_records_arrive(): void
    {
        $this->actingAs($this->makeAdmin());

        $this->createContactRequest();
        $this->createContactRequest();
        $this->travel(1)->minutes();

        Livewire::test(ListContactRequests::class)->assertOk();
        $this->assertNull(ContactRequestResource::getNavigationBadge());

        $this->travel(1)->minutes();
        $this->createContactRequest();

        $this->assertSame('1', ContactRequestResource::getNavigationBadge());
    }

    public function test_menu_shows_no_badge_on_the_list_page_itself(): void
    {
        $this->actingAs($this->makeAdmin());

        $this->createContactRequest();
        $this->travel(1)->minutes();

        // Trang khác vẫn thấy huy hiệu; mở danh sách thì sidebar của chính trang đó đã hết huy hiệu.
        $this->get(route('filament.admin.pages.dashboard'))->assertOk()->assertSee('Mới, chưa xem');
        $this->get(ContactRequestResource::getUrl('index'))->assertOk()->assertDontSee('Mới, chưa xem');

        $this->assertNull(ContactRequestResource::getNavigationBadge());
    }

    public function test_each_admin_has_their_own_seen_marker(): void
    {
        $first = $this->makeAdmin();
        $second = $this->makeAdmin();

        $this->createContactRequest();
        $this->travel(1)->minutes();

        $this->actingAs($first);
        Livewire::test(ListContactRequests::class)->assertOk();
        $this->assertNull(ContactRequestResource::getNavigationBadge());

        $this->actingAs($second);
        $this->assertSame('1', ContactRequestResource::getNavigationBadge());
    }
}
