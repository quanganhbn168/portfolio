<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Giữ cho mọi chỗ dùng <x-responsive-image> khai báo `sizes` rõ ràng.
 * Nếu không, trình duyệt coi ảnh rộng 100vw và có thể chọn bản quá lớn hoặc (khi ô bị cắt bằng
 * object-fit: cover) quá nhỏ làm ảnh bị vỡ.
 */
class ResponsiveImageUsageTest extends TestCase
{
    public function test_every_responsive_image_declares_its_sizes_attribute(): void
    {
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views')));
        $checked = 0;

        foreach ($files as $file) {
            if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            preg_match_all('/<x-responsive-image\b.*?\/>/s', (string) file_get_contents($file->getPathname()), $matches);

            foreach ($matches[0] as $tag) {
                $checked++;
                $this->assertStringContainsString('sizes=', $tag, "Thiếu sizes trong {$file->getPathname()}: {$tag}");
            }
        }

        $this->assertGreaterThan(0, $checked);
    }

    public function test_responsive_sources_never_exceed_the_original_width(): void
    {
        $media = new \Awcodes\Curator\Models\Media([
            'path' => 'media/cms/example.png', 'ext' => 'png', 'width' => 1000, 'height' => 600,
        ]);

        $sources = \App\Support\Media\MediaUrl::responsive($media, [480, 768, 1200, 1920]);

        $this->assertNotNull($sources);
        preg_match_all('/\s(\d+)w/', $sources['srcset'], $widths);
        $this->assertSame(['480', '768', '1000'], $widths[1]);
    }
}
