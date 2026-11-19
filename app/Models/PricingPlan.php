<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Gói giá hiển thị ở bảng giá. */
class PricingPlan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['features' => 'array', 'is_featured' => 'boolean', 'is_active' => 'boolean'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    /** @return list<string> */
    public function getFeatureListAttribute(): array
    {
        return collect($this->features ?? [])->map(fn (mixed $item): string => trim(is_array($item) ? (string) ($item['text'] ?? '') : (string) $item))->filter()->values()->all();
    }
}
