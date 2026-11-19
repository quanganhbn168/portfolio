<?php

namespace App\Filament\Resources\ProjectCategories\Schemas;

use App\Filament\Forms\Components\SlugInput;
use App\Filament\Forms\SeoFields;
use App\Filament\RichEditor\ScopedAttachCuratorMediaPlugin;
use App\Models\ProjectCategory;
use App\Support\Categories\CategoryTree;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

final class ProjectCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(['default' => 1, 'lg' => 3])->components([
            Group::make([
                Section::make('Danh mục dự án')->schema([
                    TextInput::make('name')->label('Tên danh mục')->required()->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (?string $state, ?string $old, Get $get, Set $set) => SlugInput::fillFromSource($state, $old, $get, $set)),
                    SlugInput::make(),
                    Textarea::make('description')->label('Mô tả')->rows(3),
                    RichEditor::make('body')->label('Nội dung')
                        ->plugins([ScopedAttachCuratorMediaPlugin::make()])
                        ->toolbarButtons([
                            ['bold', 'italic', 'underline', 'strike', 'link'],
                            ['attachCuratorMedia', 'h2', 'h3'],
                            ['alignStart', 'alignCenter', 'alignEnd'],
                            ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                            ['table'], ['undo', 'redo'],
                        ])->resizableImages(),
                ])->columns(1),
                Section::make('SEO')->schema(collect(SeoFields::make('name', 'description'))
                    ->map(fn ($field) => $field->helperText(null))->all())->columns(1),
            ])->columnSpan(['default' => 1, 'lg' => 2]),
            Group::make([
                Section::make('Thiết lập')->schema([
                    Select::make('parent_id')->label('Danh mục cha')->placeholder('Danh mục gốc')
                        ->options(fn (?ProjectCategory $record) => CategoryTree::parentOptions(ProjectCategory::class, $record))
                        ->searchable()->preload()->nullable()
                        ->rules([fn (?ProjectCategory $record) => function (string $attribute, $value, $fail) use ($record): void {
                            if ($value !== null && ! array_key_exists($value, CategoryTree::parentOptions(ProjectCategory::class, $record))) {
                                $fail('Danh mục cha không hợp lệ.');
                            }
                        }]),
                    Toggle::make('is_active')->label('Hiển thị')->default(true),
                    TextInput::make('sort_order')->label('Thứ tự')->numeric()->minValue(0)->default(0),
                ])->columns(1),
                Section::make('Hình ảnh')->schema([
                    CuratorPicker::make('curator_media_id')->label('Ảnh đại diện')
                        ->relationship('curatorMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                    CuratorPicker::make('banner_media_id')->label('Banner')
                        ->relationship('bannerMedia', 'id')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                ])->columns(1),
            ])->columnSpan(1),
        ]);
    }
}
