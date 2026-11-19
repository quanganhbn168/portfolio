<?php

namespace Tests\Feature;

use App\Filament\Resources\Page\Pages\CreatePage;
use App\Filament\Resources\ProjectCategories\Pages\CreateProjectCategory;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Tags\Pages\CreateTag;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SlugFormsRenderTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{class-string}> */
    public static function createPages(): array
    {
        return collect([
            CreatePage::class,
            CreateProjectCategory::class,
            CreateProject::class,
            CreateTag::class,
        ])->mapWithKeys(fn (string $page) => [class_basename($page) => [$page]])->all();
    }

    #[DataProvider('createPages')]
    public function test_create_page_renders_slug_input(string $page): void
    {
        $this->artisan('app:setup-admin')->assertSuccessful();
        $admin = User::factory()->create();
        $admin->assignRole(Role::findByName('super_admin', 'web'));
        $this->actingAs($admin);
        Filament::setCurrentPanel('admin');

        Livewire::test($page)->assertOk()->assertFormFieldExists('slug');
    }
}
