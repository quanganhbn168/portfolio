<?php

namespace App\Filament\Pages\Schemas;

use App\Support\Maps\GoogleMapsUrl;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

final class ContactSettingsSchema
{
    /** @return array<int, Section> */
    public static function components(): array
    {
        return [
            Section::make('Liên hệ và mạng xã hội')
                ->icon(Heroicon::OutlinedPhone)
                ->schema([
                    TextInput::make('contact_email')->label('Email')->email()->maxLength(255),
                    Repeater::make('phones')
                        ->label('Danh sách số điện thoại')
                        ->schema([
                            TextInput::make('label')->label('Nhãn')->placeholder('Hotline / Kinh doanh')->maxLength(100),
                            TextInput::make('number')->label('Số điện thoại')->tel()->required()->placeholder('0982 123 456')->maxLength(30),
                            Toggle::make('is_primary')->label('Số chính')->default(false),
                        ])
                        ->columns(3)
                        ->addActionLabel('Thêm số điện thoại')
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['number'] ?? 'Số điện thoại mới')
                        ->columnSpanFull(),
                    Repeater::make('branches')
                        ->label('Danh sách địa chỉ / chi nhánh (địa điểm đầu tiên là địa chỉ chính, hiện đầu tiên trên trang Liên hệ)')
                        ->schema([
                            TextInput::make('name')->label('Tên địa điểm')->required()->placeholder('Trụ sở chính')->maxLength(150),
                            Textarea::make('address')->label('Địa chỉ')->required()->rows(2)->maxLength(500),
                            TextInput::make('phone')->label('Điện thoại riêng (tuỳ chọn)')->tel()->maxLength(60),
                            TextInput::make('map_url')->label('Link nhúng Google Maps (tuỳ chọn)')->maxLength(1500)
                                ->helperText('Google Maps → Chia sẻ → Nhúng bản đồ, dán link hoặc cả thẻ iframe. Để trống thì bản đồ tự tạo theo địa chỉ.'),
                            Toggle::make('is_active')->label('Hiển thị')->default(true),
                        ])
                        ->columns(2)
                        ->addActionLabel('Thêm địa chỉ')
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Địa điểm mới')
                        ->columnSpanFull(),
                    Textarea::make('google_maps_embed_url')
                        ->label('Google Maps embed URL')
                        ->helperText('Dán URL embed hoặc nguyên thẻ <iframe>. Nếu chỉ có link share bên dưới, hệ thống sẽ lấy tọa độ từ link khi lưu và tạo embed cố định.')
                        ->rules([GoogleMapsUrl::embedValidationRule()])
                        ->maxLength(10000)
                        ->rows(5)
                        ->columnSpanFull(),
                    TextInput::make('google_maps_url')
                        ->label('Google Maps link')
                        ->helperText('Link chia sẻ để khách mở vị trí trên Google Maps, ví dụ https://maps.app.goo.gl/M1iQjB52X9NqzBYd7.')
                        ->url()
                        ->maxLength(2048)
                        ->columnSpanFull(),
                    TextInput::make('facebook_url')->label('Facebook')->url()->maxLength(2048),
                    TextInput::make('zalo_url')->label('Zalo')->url()->maxLength(2048),
                    TextInput::make('youtube_url')->label('YouTube')->url()->maxLength(2048),
                    TextInput::make('github_url')->label('GitHub')->url()->maxLength(2048),
                    TextInput::make('tiktok_url')->label('TikTok')->url()->maxLength(2048),
                    TextInput::make('telegram_url')->label('Telegram')->url()->maxLength(2048),
                ])
                ->columns(2),
            Section::make('Tôi là ai')
                ->description('Hiện ở khối giới thiệu bản thân trên trang chủ, cùng các mạng xã hội bên dưới.')
                ->schema([
                    TextInput::make('owner_name')->label('Họ tên')->maxLength(120),
                    TextInput::make('owner_title')->label('Chức danh')->maxLength(160),
                    TextInput::make('owner_workplace')->label('Làm việc tại')->maxLength(160),
                    TextInput::make('owner_location')->label('Sống tại')->maxLength(160),
                    Textarea::make('owner_bio')->label('Giới thiệu ngắn')->rows(5)->maxLength(1200)->columnSpanFull(),
                    CuratorPicker::make('owner_cover_media_id')->label('Ảnh bìa (rộng, tỉ lệ khoảng 3:1)')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                    CuratorPicker::make('owner_media_id')->label('Ảnh đại diện')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                ])
                ->columns(2),
            Section::make('Bảng giá & dịch vụ')
                ->description('Website chính có bảng giá, dịch vụ (ví dụ webappbacninh.vn). Link này hiện ở header, trang chủ, chi tiết dự án và footer.')
                ->schema([
                    TextInput::make('services_url')->label('Link bảng giá & dịch vụ')->url()->maxLength(2048),
                    TextInput::make('services_label')->label('Chữ trên nút')->maxLength(60)->placeholder('Bảng giá & dịch vụ'),
                ])
                ->columns(2),
        ];
    }
}
