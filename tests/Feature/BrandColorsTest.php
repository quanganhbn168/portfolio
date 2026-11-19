<?php

namespace Tests\Feature;

use App\Support\Branding\BrandColors;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandColorsTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_page_prints_the_palette_as_css_variables(): void
    {
        $this->get(route('home'))->assertOk()
            ->assertSee('id="brand-colors"', false)
            ->assertSee('--brand-primary:'.config('brand.colors.primary'), false)
            ->assertSee('--brand-secondary-rgb:', false);
    }

    public function test_changing_the_config_changes_the_output_without_touching_css(): void
    {
        config(['brand.colors.primary' => '#112233']);

        $this->assertStringContainsString('--brand-primary:#112233;--brand-primary-rgb:17, 34, 51;', BrandColors::css());
        $this->assertSame('#112233', BrandColors::hex('primary'));
    }

    public function test_invalid_values_are_ignored_so_nothing_can_inject_css(): void
    {
        config(['brand.colors' => ['primary' => '#0e3283', 'bad' => 'red;} body{display:none', 'Worse Name' => '#ffffff']]);

        $css = BrandColors::css();

        $this->assertStringContainsString('--brand-primary:#0e3283', $css);
        $this->assertStringNotContainsString('bad', $css);
        $this->assertStringNotContainsString('display:none', $css);
        $this->assertSame('#abcdef', BrandColors::hex('missing', '#abcdef'));
    }

    public function test_header_has_no_top_bar_and_keeps_the_contact_button(): void
    {
        $this->get(route('home'))->assertOk()
            ->assertDontSee('topbar', false)
            ->assertSee('header__cta', false);
    }

    public function test_frontend_stylesheets_do_not_hard_code_brand_colours(): void
    {
        $palette = array_values(BrandColors::all());
        $offenders = [];

        foreach (glob(resource_path('css/frontend/{,*/}*.css'), GLOB_BRACE) as $file) {
            $css = strtolower((string) file_get_contents($file));

            foreach ($palette as $hex) {
                if (str_contains($css, $hex)) {
                    $offenders[] = basename($file).' chứa '.$hex;
                }
            }
        }

        $this->assertSame([], $offenders, 'Màu thương hiệu phải đọc từ biến --brand-*, không viết cứng trong CSS.');
    }
}
