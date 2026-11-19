<?php

namespace App\Filament\Resources\PricingPlans\Schemas;

use App\Models\PricingPlan;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class PricingPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Gói giá')->schema([
                TextInput::make('name')->label('Tên gói')->required()->maxLength(255)->placeholder('Landing Page, Website doanh nghiệp…'),
                TextInput::make('price')->label('Giá')->required()->maxLength(255)->placeholder('3.000.000đ hoặc Liên hệ'),
                TextInput::make('price_note')->label('Ghi chú giá')->maxLength(255)->placeholder('/ website, từ…'),
                Textarea::make('description')->label('Mô tả ngắn')->rows(3)->maxLength(500),
                Repeater::make('features')->label('Quyền lợi trong gói')->simple(
                    TextInput::make('text')->label('Quyền lợi')->required()->maxLength(255),
                )->defaultItems(0)->addActionLabel('Thêm dòng')->reorderable(),
                TextInput::make('button_label')->label('Chữ trên nút')->maxLength(60)->placeholder('Nhận báo giá'),
                Toggle::make('is_featured')->label('Gói nổi bật (hiện nhãn "Phổ biến")'),
                TextInput::make('sort_order')->label('Thứ tự')->numeric()->integer()->minValue(0)->default(fn (): int => ((int) PricingPlan::max('sort_order')) + 10),
                Toggle::make('is_active')->label('Hiển thị')->default(true),
            ])->columns(1),
        ]);
    }
}
