<?php

namespace App\Filament\Resources\PricingPlans\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

final class PricingPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Gói')->searchable(),
                TextColumn::make('price')->label('Giá'),
                IconColumn::make('is_featured')->label('Nổi bật')->boolean(),
                TextColumn::make('sort_order')->label('Thứ tự')->sortable(),
                ToggleColumn::make('is_active')->label('Hiển thị'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
