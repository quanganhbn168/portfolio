<?php

namespace App\Filament\Pages\Schemas;

use App\Models\Menu;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

final class WebsiteSettingsSchema
{
    /** @return array<int, Section> */
    public static function components(): array
    {
        return [
            Section::make('Nhận diện website')
                ->icon(Heroicon::OutlinedPhoto)
                ->description('Tên website, logo và favicon được dùng xuyên suốt frontend lẫn khu quản trị.')
                ->schema([
                    TextInput::make('site_name')->label('Tên website')->required()->maxLength(255),
                    TextInput::make('tagline')->label('Tagline')->helperText('Hiện ở thanh trên cùng của website.')->maxLength(255),
                    Textarea::make('footer_description')->label('Mô tả chân trang')->rows(3)->maxLength(500)->columnSpanFull(),
                    CuratorPicker::make('logo_media_id')->label('Logo')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                    CuratorPicker::make('favicon_media_id')->label('Favicon nguồn')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Khi lưu, hệ thống chuyển đổi file upload và ghi đè trực tiếp bộ favicon cố định trong public.'),
                ])
                ->columns(2),
            Section::make('Banner')
                ->schema([
                    CuratorPicker::make('banner_media_id')->label('Ảnh mặc định cho nội dung động')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Chỉ dùng cho nội dung động chưa có ảnh riêng; không dùng cho 6 trang hệ thống.'),
                ])->columns(2),
            Section::make('Nền footer')
                ->schema([
                    CuratorPicker::make('footer_background_media_id')->label('Ảnh nền footer')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Tải lên hoặc chọn ảnh nền. Ảnh có lớp phủ tối để dễ đọc chữ; để trống dùng nền hiện tại.'),
                ]),
            Section::make('Menu hiển thị')
                ->icon(Heroicon::OutlinedBars3)
                ->schema([
                    Select::make('header_menu_id')
                        ->label('Menu header')
                        ->options(fn (): array => self::menuOptions())
                        ->searchable()
                        ->preload()
                        ->placeholder('Chọn menu cho header'),
                    Select::make('footer_menu_id')
                        ->label('Menu footer')
                        ->options(fn (): array => self::menuOptions())
                        ->searchable()
                        ->preload()
                        ->placeholder('Chọn menu cho footer'),
                ])
                ->columns(2),
        ];
    }

    /** @return array<int, string> */
    private static function menuOptions(): array
    {
        return Menu::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Menu $menu): array => [
                $menu->getKey() => $menu->location
                    ? $menu->name.' · '.$menu->location
                    : $menu->name,
            ])
            ->all();
    }
}
