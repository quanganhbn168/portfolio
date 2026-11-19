<?php

namespace App\Filament\Pages\Schemas;

use App\Filament\Forms\Components\GalleryPicker;
use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

final class AboutSettingsSchema
{
    /** Font Awesome solid icons selectable for value/culture cards. */
    public const ICONS = [
        'fa-users' => 'Con người',
        'fa-gear' => 'Bánh răng',
        'fa-chart-simple' => 'Biểu đồ',
        'fa-shield-halved' => 'Khiên',
        'fa-lightbulb' => 'Bóng đèn',
        'fa-handshake' => 'Bắt tay',
        'fa-gem' => 'Kim cương',
        'fa-user-check' => 'Chủ động',
        'fa-book-open' => 'Học hỏi',
        'fa-heart' => 'Trái tim',
        'fa-star' => 'Ngôi sao',
        'fa-award' => 'Giải thưởng',
        'fa-bolt' => 'Tia chớp',
        'fa-seedling' => 'Phát triển',
        'fa-eye' => 'Con mắt',
        'fa-rocket' => 'Tên lửa',
        'fa-industry' => 'Nhà máy',
        'fa-building' => 'Toà nhà',
        'fa-chart-line' => 'Tăng trưởng',
        'fa-bullseye' => 'Mục tiêu',
        'fa-certificate' => 'Chứng nhận',
        'fa-medal' => 'Huy chương',
    ];

    /**
     * Các khối xếp theo đúng thứ tự hiển thị trên trang; mỗi khối giữ tiêu đề, nội dung và ảnh của riêng nó.
     *
     * @return array<int, Section>
     */
    public static function components(): array
    {
        return [
            Section::make('1. Đầu trang (Hero)')
                ->icon(Heroicon::OutlinedDocumentText)
                ->description('Tiêu đề lấy từ trang hệ thống "Giới thiệu" (từ cuối được tô xanh lá). Dòng mô tả bên dưới tiêu đề; ảnh bên phải là ảnh banner của trang "Giới thiệu" hoặc ảnh hero dưới đây.')
                ->schema([
                    CuratorPicker::make('default_image_media_id')->label('Ảnh hero')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Cũng là ảnh dự phòng cho các khối chưa chọn ảnh riêng.'),
                    Textarea::make('page_intro')->label('Dòng mô tả dưới tiêu đề')->rows(2)->columnSpanFull(),
                    TextInput::make('hero_cta_label')->label('Nhãn nút liên hệ')->maxLength(100),
                    TextInput::make('hero_video_label')->label('Nhãn nút xem video')->maxLength(100)->helperText('Chỉ hiện khi đã cấu hình video ở khối Câu chuyện.'),
                ])
                ->columns(2),
            Section::make('2. Tổng quan công ty (Company Overview)')
                ->icon(Heroicon::OutlinedInformationCircle)
                ->description('Ảnh bên trái, nội dung và hàng số liệu bên phải. Để trống ảnh thì nội dung chiếm toàn chiều ngang.')
                ->schema([
                    CuratorPicker::make('story_image_media_id')->label('Ảnh tổng quan')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Nên dùng ảnh dọc, hiển thị đúng tỷ lệ gốc.'),
                    TextInput::make('story_eyebrow')->label('Nhãn nhỏ phía trên (VD: Our Story)')->maxLength(100),
                    TextInput::make('story_title')->label('Tiêu đề')->maxLength(255),
                    RichEditor::make('story')
                        ->label('Nội dung')
                        ->plugins([ScopedAttachCuratorMediaPlugin::make()])
                        ->enableToolbarButtons(['attachCuratorMedia'])
                        ->disableToolbarButtons(['attachFiles'])
                        ->columnSpanFull(),
                    Repeater::make('page_stats')
                        ->label('Hàng số liệu')
                        ->schema([
                            Select::make('icon')->label('Biểu tượng')->options(self::ICONS)->searchable()->required(),
                            TextInput::make('value')->label('Số liệu (VD: 10+, ISO 9001)')->required()->maxLength(40),
                            TextInput::make('label')->label('Chú thích')->required()->maxLength(80),
                        ])
                        ->columns(3)
                        ->defaultItems(0)
                        ->maxItems(5)
                        ->addActionLabel('Thêm số liệu')
                        ->reorderable()
                        ->itemLabel(fn (array $state): ?string => trim(($state['value'] ?? '').' '.($state['label'] ?? '')) ?: null)
                        ->columnSpanFull(),
                    TextInput::make('services_link_label')->label('Nhãn nút xem năng lực (để trống thì ẩn nút)')->maxLength(255)->columnSpanFull(),
                    Select::make('video_source')
                        ->label('Video giới thiệu')
                        ->options(['youtube' => 'YouTube', 'upload' => 'Video tải lên'])
                        ->placeholder('Không hiển thị video')
                        ->live()
                        ->helperText('Nút play nằm trên ảnh câu chuyện và nút "Xem video" ở hero.')
                        ->columnSpanFull(),
                    TextInput::make('video_youtube_url')
                        ->label('URL video YouTube')
                        ->url()
                        ->maxLength(2048)
                        ->visible(fn ($get): bool => $get('video_source') === 'youtube')
                        ->required(fn ($get): bool => $get('video_source') === 'youtube')
                        ->columnSpanFull(),
                    CuratorPicker::make('video_media_id')->label('Video tải lên')->disk('public')->constrained()->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])->columnSpanFull()->visible(fn ($get): bool => $get('video_source') === 'upload')->required(fn ($get): bool => $get('video_source') === 'upload'),
                    CuratorPicker::make('video_poster_media_id')->label('Ảnh bìa video')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->visible(fn ($get): bool => filled($get('video_source')))->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('2b. Thư của lãnh đạo')
                ->icon(Heroicon::OutlinedEnvelope)
                ->description('Để trống tiêu đề và nội dung thì khối không hiển thị. Nội dung bên trái, ảnh bên phải.')
                ->schema([
                    TextInput::make('leader_eyebrow')->label('Nhãn nhỏ (VD: Chairman\'s Message)')->maxLength(100),
                    TextInput::make('leader_title')->label('Tiêu đề')->maxLength(255),
                    RichEditor::make('leader_message')->label('Nội dung thư')->columnSpanFull(),
                    TextInput::make('leader_name')->label('Người ký (dòng dưới chữ ký)')->maxLength(255),
                    CuratorPicker::make('leader_image_media_id')->label('Ảnh bên phải')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                    CuratorPicker::make('leader_signature_media_id')->label('Ảnh chữ ký (PNG nền trong suốt)')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                ])
                ->columns(2),
            Section::make('3. Tầm nhìn')
                ->icon(Heroicon::OutlinedEye)
                ->description('Thẻ thứ nhất trong hàng ba thẻ Tầm nhìn / Sứ mệnh / Giá trị cốt lõi. Ảnh không dùng ở thẻ này.')
                ->schema([
                    TextInput::make('vision_title')->label('Tiêu đề')->maxLength(255)->columnSpanFull(),
                    CuratorPicker::make('vision_image_media_id')->label('Ảnh tầm nhìn')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Để trống sẽ dùng ảnh hero.'),
                    Textarea::make('vision')->label('Nội dung tầm nhìn')->rows(4)->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('4. Sứ mệnh')
                ->icon(Heroicon::OutlinedRocketLaunch)
                ->description('Thẻ thứ hai trong hàng ba thẻ. Ảnh không dùng ở thẻ này.')
                ->schema([
                    TextInput::make('mission_title')->label('Tiêu đề')->maxLength(255)->columnSpanFull(),
                    CuratorPicker::make('mission_image_media_id')->label('Ảnh sứ mệnh')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Để trống sẽ dùng ảnh hero.'),
                    Textarea::make('mission')->label('Nội dung sứ mệnh')->rows(4)->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('5. Giá trị cốt lõi')
                ->icon(Heroicon::OutlinedSparkles)
                ->description('Thẻ thứ ba: hiện danh sách tên các giá trị kèm dấu tích (mô tả không hiện ở thẻ này).')
                ->schema([
                    TextInput::make('principles_title')->label('Tiêu đề thẻ (VD: Our Core Values)')->maxLength(255),
                    self::iconItemsRepeater('core_values_items', 'Các giá trị', 'Thêm giá trị'),
                ])
                ->columns(2),
            Section::make('6. Văn hóa doanh nghiệp (tuỳ chọn)')
                ->icon(Heroicon::OutlinedUserGroup)
                ->description('Hiện bên dưới phần lịch sử. Để trống tiêu đề và danh sách thì khối không hiển thị.')
                ->schema([
                    CuratorPicker::make('culture_image_media_id')->label('Ảnh văn hóa')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                    TextInput::make('culture_title')->label('Tiêu đề')->maxLength(255)->columnSpanFull(),
                    Textarea::make('culture_description')->label('Mô tả')->rows(3)->columnSpanFull(),
                    self::iconItemsRepeater('culture_items', 'Các nét văn hóa', 'Thêm nét văn hóa'),
                ])
                ->columns(2),
            Section::make('7. Lịch sử hình thành')
                ->icon(Heroicon::OutlinedClock)
                ->description('Hiện dạng timeline ngang trên trang. Mỗi mốc có năm, tiêu đề, mô tả và biểu tượng.')
                ->schema([
                    TextInput::make('history_eyebrow')->label('Nhãn nhỏ (VD: Our Milestones)')->maxLength(100),
                    TextInput::make('history_title')->label('Tiêu đề khối')->maxLength(255),
                    Textarea::make('history_description')->label('Mô tả khối (tuỳ chọn)')->rows(2)->columnSpanFull(),
                    Textarea::make('history')
                        ->label('Nội dung lịch sử đơn')
                        ->helperText('Chỉ dùng khi không có danh sách mốc bên dưới.')
                        ->rows(5)
                        ->columnSpanFull(),
                    Repeater::make('history_timeline')
                        ->label('Các mốc lịch sử')
                        ->schema([
                            TextInput::make('year')->label('Năm')->required()->maxLength(30),
                            Select::make('icon')->label('Biểu tượng')->options(self::ICONS)->searchable()->default('fa-industry'),
                            TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)->columnSpanFull(),
                            Textarea::make('description')->label('Mô tả')->required()->rows(4)->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->addActionLabel('Thêm mốc lịch sử')
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): string => trim(($state['year'] ?? 'Mốc mới').' — '.($state['title'] ?? '')))
                        ->columnSpanFull(),
                ])
                ->collapsed(),
            Section::make('8. Không gian văn phòng (tuỳ chọn)')
                ->icon(Heroicon::OutlinedBuildingOffice2)
                ->description('Để trống gallery thì khối không hiển thị.')
                ->schema([
                    TextInput::make('office_title')->label('Tiêu đề khối')->maxLength(255),
                    Textarea::make('office_description')->label('Mô tả')->rows(3)->columnSpanFull(),
                    GalleryPicker::make('office_gallery')->label('Gallery văn phòng')->multiple()->disk('public')->constrained()->acceptedFileTypes(['image/*'])->helperText('Chọn nhiều ảnh và kéo thả để đổi thứ tự. Nút “Xóa tất cả” luôn yêu cầu xác nhận.')->columnSpanFull(),
                ])
                ->columns(2),
        ];
    }

    private static function iconItemsRepeater(string $name, string $label, string $addLabel): Repeater
    {
        return Repeater::make($name)
            ->label($label)
            ->schema([
                Select::make('icon')->label('Biểu tượng')->options(self::ICONS)->searchable()->required(),
                TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255),
                Textarea::make('description')->label('Mô tả')->rows(3)->columnSpanFull(),
            ])
            ->columns(2)
            ->defaultItems(0)
            ->addActionLabel($addLabel)
            ->reorderable()
            ->collapsible()
            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
            ->columnSpanFull();
    }
}
