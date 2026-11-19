<?php

namespace App\Filament\Resources\PricingPlans;

use App\Filament\Resources\Concerns\UsesPrimaryKeyForRecordRoutes;
use App\Filament\Resources\PricingPlans\Pages\CreatePricingPlan;
use App\Filament\Resources\PricingPlans\Pages\EditPricingPlan;
use App\Filament\Resources\PricingPlans\Pages\ListPricingPlans;
use App\Filament\Resources\PricingPlans\Schemas\PricingPlanForm;
use App\Filament\Resources\PricingPlans\Tables\PricingPlansTable;
use App\Models\PricingPlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PricingPlanResource extends Resource
{
    use UsesPrimaryKeyForRecordRoutes;

    protected static ?string $model = PricingPlan::class;

    protected static ?string $recordRouteKeyName = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?string $navigationLabel = 'Bảng giá';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Dịch vụ & bảng giá';
    }

    public static function getModelLabel(): string
    {
        return 'gói giá';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Bảng giá';
    }

    public static function form(Schema $schema): Schema
    {
        return PricingPlanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PricingPlansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPricingPlans::route('/'),
            'create' => CreatePricingPlan::route('/create'),
            'edit' => EditPricingPlan::route('/{record}/edit'),
        ];
    }
}
