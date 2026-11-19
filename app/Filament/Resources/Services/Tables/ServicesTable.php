<?php

namespace App\Filament\Resources\Services\Tables;

use App\Models\Service;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

final class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Dịch vụ')->searchable()->wrap(),
                TextColumn::make('icon')->label('Biểu tượng')->formatStateUsing(fn (string $state): string => Service::ICONS[$state] ?? $state)->badge(),
                TextColumn::make('sort_order')->label('Thứ tự')->sortable(),
                ToggleColumn::make('is_active')->label('Hiển thị'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
