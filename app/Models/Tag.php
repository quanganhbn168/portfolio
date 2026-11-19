<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saving(function (Tag $tag): void {
            if (blank($tag->slug) && filled($tag->name)) {
                $base = Str::slug($tag->name);
                $slug = $base;
                $suffix = 2;

                while (static::query()->where('slug', $slug)->when($tag->exists, fn (Builder $query) => $query->whereKeyNot($tag->getKey()))->exists()) {
                    $slug = $base.'-'.$suffix++;
                }

                $tag->slug = $slug;
            }
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
