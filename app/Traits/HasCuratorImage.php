<?php

namespace App\Traits;

use App\Support\Media\MediaUrl;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ảnh đại diện từ Curator (cột `curator_media_id`) và accessor `image_url` dùng chung cho frontend.
 * Khi đọc danh sách, luôn eager load `curatorMedia` để tránh N+1.
 */
trait HasCuratorImage
{
    public function curatorMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'curator_media_id');
    }

    /** Ảnh gốc: dùng cho ảnh chia sẻ mạng xã hội (og:image) và các chỗ cần đúng file đã tải lên. */
    public function getImageUrlAttribute(): ?string
    {
        return MediaUrl::resolve($this->curatorMedia);
    }

    /** WebP lớn (1600px) cho ảnh bìa, ảnh chi tiết, ảnh nền. */
    public function getImageWebpUrlAttribute(): ?string
    {
        return MediaUrl::webp($this->curatorMedia, 1600);
    }

    /** WebP vừa (768px) cho ảnh trong thẻ danh sách. */
    public function getImageCardUrlAttribute(): ?string
    {
        return MediaUrl::webp($this->curatorMedia, 768);
    }

    /** WebP nhỏ (320px) cho thumbnail. */
    public function getImageThumbUrlAttribute(): ?string
    {
        return MediaUrl::webp($this->curatorMedia, 320);
    }
}
