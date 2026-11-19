<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Slug extends Model
{
    /** Đường dẫn đã dành cho route cố định, không được dùng làm slug nội dung. */
    public const RESERVED = [
        'capabilities',
        'solutions',
        'projects',
        'products',
        'about',
        'blog',
        'services',
        'pricing',
        'contact',
        'news',
        'careers',
        'search',
        'comments',
        'newsletter',
        'language',
        'apply',
        'nang-luc',
        'giai-phap',
        'du-an',
        'bang-gia',
        'gioi-thieu',
        'lien-he',
        'tin-tuc',
        'san-pham',
        'sitemap.xml',
        'robots.txt',
        'tim-kiem',
        'tuyen-dung',
        'bai-gioi-thieu',
        'binh-luan',
        'admin',
        'livewire',
        'storage',
        'build',
        'favicon.ico',
        'up',
    ];

    protected $fillable = [
        'slug',
    ];

    public static function isReserved(string $slug): bool
    {
        return in_array($slug, self::RESERVED, true);
    }

    public function sluggable(): MorphTo
    {
        return $this->morphTo();
    }
}
