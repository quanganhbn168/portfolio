<?php

namespace App\Models;

use App\Traits\HasCuratorImage;
use App\Traits\HasSeoImage;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory, HasSlug;
    use HasCuratorImage;
    use HasSeoImage;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'details' => 'array',
            'is_featured' => 'boolean',
            'completed_at' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where(fn (Builder $query) => $query
            ->whereNull('published_at')
            ->orWhere('published_at', '<=', now()));
    }
}
