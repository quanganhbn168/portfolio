<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Filament\Forms\Components\SlugInput;
use App\Filament\Forms\SeoFields;
use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Support\Seo\ContentSeoFallbacks;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(['lg' => 3])->components([
            Group::make([
                Section::make('Bài viết')->schema([
                    TextInput::make('title')->label('Tiêu đề')->required()->maxLength(255)
                        ->live(onBlur: true)->afterStateUpdated(function (?string $state, ?string $old, $get, $set): void {
                            SlugInput::fillFromSource($state, $old, $get, $set);
                            if (blank($get('seo_title'))) {
                                $set('seo_title', ContentSeoFallbacks::title($state));
                            }
                        }),
                    SlugInput::make(),
                    Textarea::make('excerpt')->label('Tóm tắt')->rows(3)->helperText('Hiện ở danh sách bài viết và khi chia sẻ.'),
                    RichEditor::make('body')->label('Nội dung')
                        ->helperText('Viết thoải mái: tiêu đề, danh sách, trích dẫn, bảng, code, ảnh…')
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
                Section::make('SEO')->schema(SeoFields::make())->columns(1),
            ])->columnSpan(['lg' => 2]),
            Group::make([
                Section::make('Đăng bài')->schema([
                    Select::make('status')->label('Trạng thái')->options(['draft' => 'Bản nháp', 'published' => 'Đã xuất bản', 'private' => 'Riêng tư'])->required()->default('draft'),
                    DateTimePicker::make('published_at')->label('Ngày đăng')->default(now()),
                    CuratorPicker::make('curator_media_id')->label('Ảnh bìa')->disk('public')->constrained()->acceptedFileTypes(['image/*'])->relationship('curatorMedia', 'id'),
                ]),
            ])->columnSpan(['lg' => 1]),
        ]);
    }
}
