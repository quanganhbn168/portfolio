<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactRequests\ContactRequestResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\ContactRequest;
use App\Models\Project;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WebsiteStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {

        return [
            Stat::make('Dự án đang hiển thị', Project::query()->published()->count())
                ->description('Hồ sơ năng lực công khai')
                ->descriptionIcon(Heroicon::OutlinedPhoto)
                ->color('success')
                ->url(ProjectResource::getUrl('index')),
            Stat::make('Yêu cầu tư vấn', ContactRequest::query()->count())
                ->description('Khách hàng đã liên hệ')
                ->descriptionIcon(Heroicon::OutlinedChatBubbleLeftRight)
                ->color('primary')
                ->url(ContactRequestResource::getUrl('index')),
        ];
    }
}
