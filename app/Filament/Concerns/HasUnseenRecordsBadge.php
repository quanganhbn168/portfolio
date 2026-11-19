<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Huy hiệu cạnh mục menu đếm số bản ghi mới đến kể từ lần cuối tài khoản đang đăng nhập mở danh sách.
 * Mở danh sách là huy hiệu mất, không phụ thuộc trạng thái xử lý. Mỗi tài khoản có mốc "đã xem" riêng.
 */
trait HasUnseenRecordsBadge
{
    public static function getNavigationBadge(): ?string
    {
        $count = static::unseenRecordsQuery()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Mới, chưa xem';
    }

    public static function markRecordsAsSeen(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $seen = $user->navigation_seen_at ?? [];
        $seen[static::getSlug()] = now()->toIso8601String();

        $user->forceFill(['navigation_seen_at' => $seen])->saveQuietly();
    }

    protected static function unseenRecordsQuery(): Builder
    {
        $seenAt = auth()->user()?->navigation_seen_at[static::getSlug()] ?? null;

        return static::getModel()::query()
            ->when($seenAt, fn (Builder $query) => $query->where('created_at', '>', Carbon::parse($seenAt)));
    }
}
