<?php

namespace Database\Seeders\Concerns;

use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\Storage;

/** Chép ảnh SVG mẫu trong database/seeders/images vào disk public và tạo bản ghi thư viện media. */
trait ImportsSeedMedia
{
    private function media(string $name, string $title, int $width = 1200, int $height = 750): int
    {
        $filename = $name.'.svg';
        $source = database_path('seeders/images/'.$filename);
        $disk = Storage::disk('public');

        if (! $disk->exists($filename)) {
            $disk->put($filename, file_get_contents($source));
        }

        return Media::query()->firstOrCreate(['disk' => 'public', 'path' => $filename], [
            'directory' => null, 'visibility' => 'public', 'name' => $name,
            'width' => $width, 'height' => $height, 'size' => filesize($source) ?: 0,
            'type' => 'image/svg+xml', 'ext' => 'svg', 'alt' => $title, 'title' => $title,
        ])->getKey();
    }
}
