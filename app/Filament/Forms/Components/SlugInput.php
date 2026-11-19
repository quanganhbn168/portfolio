<?php

namespace App\Filament\Forms\Components;

use App\Models\Slug;
use App\Support\Seo\ContentSeoFallbacks;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Model;

/**
 * Ô nhập đường dẫn (slug) dùng cho mọi form quản trị.
 *
 * - Khi sửa, hiển thị slug hiện tại của bản ghi (kể cả slug nằm trong bảng `slugs` dùng chung).
 * - Tự chuẩn hoá về chữ thường, số và dấu gạch ngang khi rời ô và khi lưu.
 * - Báo lỗi ngay trong form khi slug là đường dẫn hệ thống hoặc đã có trang khác dùng,
 *   thay vì để hệ thống tự thêm hậu tố -2, -3.
 */
class SlugInput extends TextInput
{
    public static function getDefaultName(): ?string
    {
        return 'slug';
    }

    /**
     * Gọi trong afterStateUpdated của ô tiêu đề/tên: slug chạy theo tiêu đề, kể cả khi sửa,
     * trừ khi slug đã được sửa tay (không còn khớp với slug tạo từ tiêu đề cũ).
     */
    public static function fillFromSource(?string $source, ?string $oldSource, Get $get, Set $set, string $path = 'slug'): void
    {
        $current = $get($path);

        if (filled($current) && $current !== ContentSeoFallbacks::slug($oldSource)) {
            return;
        }

        if ($slug = ContentSeoFallbacks::slug($source)) {
            $set($path, $slug);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Đường dẫn (slug)')
            ->prefix(fn (): string => rtrim(url('/'), '/').'/')
            ->maxLength(180)
            ->live(onBlur: true)
            ->formatStateUsing(fn (?string $state, ?Model $record): ?string => $state ?: $record?->slug)
            ->afterStateUpdated(fn (?string $state, SlugInput $component) => $component->state(ContentSeoFallbacks::slug($state)))
            ->dehydrateStateUsing(fn (?string $state): ?string => ContentSeoFallbacks::slug($state))
            ->rule(fn (SlugInput $component): Closure => function (string $attribute, mixed $value, Closure $fail) use ($component): void {
                $slug = ContentSeoFallbacks::slug($value);

                if ($slug === null) {
                    return;
                }

                if (Slug::isReserved($slug)) {
                    $fail("Đường dẫn \"{$slug}\" là đường dẫn hệ thống, vui lòng chọn đường dẫn khác.");

                    return;
                }

                if ($component->isSlugTaken($slug)) {
                    $fail("Đường dẫn \"{$slug}\" đã được dùng cho trang khác.");
                }
            });
    }

    public function isSlugTaken(string $slug): bool
    {
        $modelClass = $this->getModel();

        if ($modelClass === null) {
            return false;
        }

        $record = $this->getRecord();
        $record = $record instanceof Model && $record->exists ? $record : null;

        // Model dùng bảng slug chung (HasSlug): slug phải duy nhất trên toàn site vì mọi trang nằm ở /{slug}.
        if (method_exists($modelClass, 'slugs')) {
            return Slug::query()
                ->where('slug', $slug)
                ->when($record, fn ($query) => $query->where(fn ($query) => $query
                    ->where('sluggable_type', '!=', $record->getMorphClass())
                    ->orWhere('sluggable_id', '!=', $record->getKey())))
                ->exists();
        }

        // Model lưu slug ở cột riêng (ví dụ thẻ): chỉ cần duy nhất trong bảng của nó.
        return $modelClass::query()
            ->where('slug', $slug)
            ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
            ->exists();
    }
}
