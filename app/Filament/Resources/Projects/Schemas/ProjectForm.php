<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Forms\Components\SlugInput;
use App\Filament\Forms\SeoFields;
use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Models\Project;
use App\Support\Seo\ContentSeoFallbacks;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(['lg' => 3])->components([
            Group::make([
                Section::make('Thông tin dự án')->schema([
                    TextInput::make('title')->label('Tên dự án')->required()->maxLength(255)
                        ->live(onBlur: true)->afterStateUpdated(function (?string $state, ?string $old, $get, $set): void {
                            SlugInput::fillFromSource($state, $old, $get, $set);
                            if (blank($get('seo_title'))) {
                                $set('seo_title', ContentSeoFallbacks::title($state));
                            }
                        }),
                    SlugInput::make(),
                    Textarea::make('excerpt')->label('Mô tả trên banner')->rows(3),
                    TextInput::make('client_name')->label('Khách hàng')->maxLength(255),
                    TextInput::make('industry')->label('Loại dự án')->maxLength(255)->placeholder('Website doanh nghiệp, Landing page, Phần mềm…'),
                    TextInput::make('price')->label('Giá trị / chi phí')->maxLength(255)->placeholder('Ví dụ: 25.000.000đ'),
                    Select::make('project_status')->label('Tình trạng')->options(Project::PROJECT_STATUSES)->default('completed')->required(),
                    DatePicker::make('started_at')->label('Bắt đầu từ'),
                    DatePicker::make('completed_at')->label('Hoàn thành'),
                    TextInput::make('technologies')->label('Công nghệ sử dụng')->maxLength(512)->placeholder('Laravel, MySQL, Tailwind CSS')->helperText('Cách nhau bằng dấu phẩy.'),
                    TextInput::make('website_url')->label('Link website thực tế')->url()->maxLength(1024)->helperText('Khách hàng bấm vào để xem dự án đang chạy thật.'),
                    TextInput::make('video_url')->label('Video giới thiệu (link YouTube)')->url(),
                    self::image('curator_media_id', 'Ảnh đại diện')->relationship('curatorMedia', 'id'),
                ]),
                Section::make('Nội dung & thư viện ảnh')->schema([
                    self::image('gallery', 'Thư viện ảnh (gallery)')->multiple(),
                    RichEditor::make('body')->label('Nội dung chi tiết')
                        ->helperText('Viết thoải mái: tiêu đề, danh sách, trích dẫn, bảng, code, ảnh… trang dự án đã được chỉnh CSS cho các định dạng này.')
                        ->plugins([ScopedAttachCuratorMediaPlugin::make()])
                        ->toolbarButtons([
                            ['bold', 'italic', 'underline', 'strike', 'link'],
                            ['h2', 'h3'],
                            ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                            ['table', 'horizontalRule', 'attachCuratorMedia'],
                            ['undo', 'redo'],
                        ])
                        ->columnSpanFull(),
                ]),
                Section::make('Thách thức')->collapsed()->schema([
                    self::cards('challenges', 'Các thách thức'),
                ]),
                Section::make('Giải pháp triển khai')->collapsed()->schema([
                    Textarea::make('details.solution.description')->label('Mô tả')->rows(3),
                    Repeater::make('details.solution.items')->label('Danh sách giải pháp')->schema([
                        TextInput::make('text')->label('Giải pháp')->required()->maxLength(255),
                    ])->defaultItems(0)->addActionLabel('Thêm giải pháp'),
                    self::image('details.solution.media_id', 'Ảnh giải pháp'),
                ]),
                Section::make('Hạng mục thi công')->collapsed()->schema([
                    self::cards('scope', 'Các hạng mục'),
                ]),
                Section::make('Ảnh thi công')->collapsed()->schema([
                    self::image('details.construction.images', 'Ảnh thi công')->multiple(),
                ]),
                Section::make('SEO')->schema(SeoFields::make())->columns(1),
            ])->columnSpan(['lg' => 2]),
            Group::make([
                Section::make('Phân loại & hiển thị')->schema([
                    Select::make('project_category_id')->label('Danh mục')->relationship('category', 'name')->searchable()->preload(),
                    Select::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'pending' => 'Chờ duyệt', 'private' => 'Riêng tư'])->required()->default('draft'),
                    DatePicker::make('published_at')->label('Ngày xuất bản'),
                    TextInput::make('sort_order')->label('Thứ tự')->numeric()->integer()->minValue(0)->default(fn (): int => ((int) Project::max('sort_order')) + 1),
                    Toggle::make('is_featured')->label('Dự án nổi bật'),
                ]),
            ])->columnSpan(['lg' => 1]),
        ]);
    }

    private static function image(string $name, string $label): CuratorPicker
    {
        return CuratorPicker::make($name)->label($label)->disk('public')->constrained()->acceptedFileTypes(['image/*']);
    }

    private static function cards(string $section, string $label): Repeater
    {
        return Repeater::make("details.{$section}.items")->label($label)->schema([
            TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255),
            Textarea::make('description')->label('Mô tả')->rows(2),
        ])->defaultItems(0)->addActionLabel('Thêm mục')->collapsible()
            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null);
    }
}
