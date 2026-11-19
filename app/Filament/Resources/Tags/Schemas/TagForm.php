<?php

namespace App\Filament\Resources\Tags\Schemas;

use App\Filament\Forms\Components\SlugInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

final class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thẻ nội dung')

                ->schema([
                    TextInput::make('name')->label('Tên thẻ')->required()->maxLength(100)->unique(ignoreRecord: true)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (?string $state, ?string $old, Get $get, Set $set) => SlugInput::fillFromSource($state, $old, $get, $set)),
                    SlugInput::make()->prefix(null)->maxLength(120),
                    Toggle::make('is_active')->label('Đang sử dụng')->default(true),
                ])
                ->columns(2),
        ]);
    }
}
