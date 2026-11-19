<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Models\Service;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dịch vụ')->schema([
                TextInput::make('title')->label('Tên dịch vụ')->required()->maxLength(255),
                Select::make('icon')->label('Biểu tượng')->options(Service::ICONS)->default('fa-code')->required()->native(false),
                Textarea::make('description')->label('Mô tả ngắn')->rows(4)->maxLength(600),
                TextInput::make('sort_order')->label('Thứ tự')->numeric()->integer()->minValue(0)->default(fn (): int => ((int) Service::max('sort_order')) + 10),
                Toggle::make('is_active')->label('Hiển thị')->default(true),
            ])->columns(1),
        ]);
    }
}
