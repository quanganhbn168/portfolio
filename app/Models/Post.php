<?php

namespace App\Models;

use App\Traits\HasCuratorImage;
use App\Traits\HasSeoImage;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Bài viết blog cá nhân. */
class Post extends Model
{
    use HasCuratorImage, HasFactory, HasSeoImage, HasSlug;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where(fn (Builder $query) => $query
            ->whereNull('published_at')
            ->orWhere('published_at', '<=', now()));
    }

    /** Thời gian đọc ước tính (khoảng 200 từ/phút), tối thiểu 1 phút. */
    public function getReadingMinutesAttribute(): int
    {
        $words = str_word_count(strip_tags((string) $this->body), 0, 'àáảãạăằắẳẵặâầấẩẫậèéẻẽẹêềếểễệìíỉĩịòóỏõọôồốổỗộơờớởỡợùúủũụưừứửữựỳýỷỹỵđ');

        return max(1, (int) ceil($words / 200));
    }
}
