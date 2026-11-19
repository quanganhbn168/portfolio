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

    /** Tình trạng triển khai của dự án (khác với trạng thái xuất bản `status`). */
    public const PROJECT_STATUSES = [
        'planning' => 'Lên kế hoạch',
        'in_progress' => 'Đang thực hiện',
        'completed' => 'Đã hoàn thành',
        'maintenance' => 'Đang bảo trì',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'details' => 'array',
            'is_featured' => 'boolean',
            'completed_at' => 'date',
            'started_at' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function getProjectStatusLabelAttribute(): string
    {
        return __(self::PROJECT_STATUSES[$this->project_status] ?? self::PROJECT_STATUSES['completed']);
    }

    /** @return list<string> */
    public function getTechnologyListAttribute(): array
    {
        return collect(explode(',', (string) $this->technologies))->map(fn (string $item): string => trim($item))->filter()->values()->all();
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
