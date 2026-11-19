<?php

namespace App\Filament\Pages\Schemas;

use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class SettingsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('website-settings')
                    ->tabs([
                        Tab::make('Website')
                            ->icon(Heroicon::OutlinedGlobeAlt)
                            ->schema(WebsiteSettingsSchema::components()),
                        Tab::make('Doanh nghiệp')
                            ->icon(Heroicon::OutlinedBuildingOffice2)
                            ->schema(CompanySettingsSchema::components()),
                        Tab::make('Trang Giới thiệu')
                            ->icon(Heroicon::OutlinedInformationCircle)
                            ->schema(AboutSettingsSchema::components()),
                        Tab::make('Liên hệ')
                            ->icon(Heroicon::OutlinedPhone)
                            ->schema(ContactSettingsSchema::components()),
                        Tab::make('SEO')
                            ->icon(Heroicon::OutlinedMagnifyingGlass)
                            ->schema(SeoSettingsSchema::components()),
                    ])
                    ->persistTabInQueryString('tab')
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }
}
