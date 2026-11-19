<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Website mặc định tiếng Anh (TestCase mặc định ép sang tiếng Việt cho các test nội dung cũ).
        config(['app.locale' => 'en']);
        app()->setLocale('en');
    }

    public function test_site_is_english_by_default_and_loads_no_third_party_translator(): void
    {
        $this->get(route('home'))->assertOk()
            ->assertSee('<html lang="en"', false)
            ->assertSee('Explore our delivered projects')
            ->assertDontSee('Khám phá các dự án đã triển khai')
            ->assertDontSee('translate.google.com', false);
    }

    public function test_vietnamese_cookie_switches_the_interface_and_loads_google_translate(): void
    {
        $this->withUnencryptedCookie('lang', 'vi')->get(route('home'))->assertOk()
            ->assertSee('<html lang="vi"', false)
            ->assertSee('Khám phá các dự án đã triển khai')
            ->assertDontSee('Explore our delivered projects')
            ->assertSee('translate.google.com', false);
    }

    public function test_language_switcher_offers_english_and_vietnamese_only(): void
    {
        $response = $this->get(route('home'))->assertOk()
            ->assertSee(route('language.switch', ['locale' => 'en']), false)
            ->assertSee(route('language.switch', ['locale' => 'vi']), false);

        $this->assertSame(2, substr_count($response->getContent(), 'action="'.url('/language/')));
    }

    public function test_choosing_vietnamese_sets_both_cookies_and_returns_to_the_page(): void
    {
        $this->post(route('language.switch', ['locale' => 'vi']), ['return_to' => '/about'])
            ->assertRedirect('/about')
            ->assertCookie('lang', 'vi', false)
            ->assertCookie('googtrans', '/en/vi', false);
    }

    public function test_choosing_english_clears_the_google_translate_cookie(): void
    {
        $this->withUnencryptedCookie('googtrans', '/en/vi')
            ->post(route('language.switch', ['locale' => 'en']))
            ->assertRedirect(route('home'))
            ->assertCookie('lang', 'en', false)
            ->assertCookieExpired('googtrans');
    }

    public function test_unsupported_language_and_off_site_return_paths_are_rejected(): void
    {
        $this->post(route('language.switch', ['locale' => 'fr']))->assertNotFound();

        $this->post(route('language.switch', ['locale' => 'vi']), ['return_to' => '//evil.example.com'])
            ->assertRedirect(route('home'));
    }

    public function test_every_visible_interface_string_has_an_english_translation(): void
    {
        $english = json_decode((string) file_get_contents(base_path('lang/en.json')), true, flags: JSON_THROW_ON_ERROR);
        $missing = [];

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views'), \FilesystemIterator::SKIP_DOTS)) as $file) {
            $path = str_replace('\\', '/', $file->getPathname());

            if (! str_ends_with($path, '.blade.php') || str_contains($path, '/views/filament/') || str_contains($path, '/views/mail/')) {
                continue;
            }

            preg_match_all("/__\\('((?:[^'\\\\\\\\]|\\\\\\\\.)*)'/", (string) file_get_contents($path), $matches);

            foreach ($matches[1] as $key) {
                $key = str_replace("\\'", "'", $key);

                if (preg_match('/[àáảãạăâèéẻẽẹêìíỉĩịòóỏõọôơùúủũụưỳýỷỹỵđ]/iu', $key) && ! array_key_exists($key, $english)) {
                    $missing[] = basename($path).': '.$key;
                }
            }
        }

        $this->assertSame([], $missing, 'Chuỗi chưa có bản dịch tiếng Anh trong lang/en.json.');
    }
}
