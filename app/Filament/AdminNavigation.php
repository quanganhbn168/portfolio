<?php

namespace App\Filament;

use App\Filament\Pages\AdminDashboard;
use App\Filament\Pages\ManageSettings;
use App\Filament\Resources\ContactRequests\ContactRequestResource;
use App\Filament\Resources\Page\PageResource;
use App\Filament\Resources\ProjectCategories\ProjectCategoryResource;
use App\Filament\Resources\Projects\ProjectResource;
use Awcodes\Curator\Resources\Media\MediaResource;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Support\Icons\Heroicon;

/**
 * Sidebar quản trị: thứ tự, nhóm, tên và icon được quyết định ở đây (một chỗ duy nhất),
 * theo luồng làm việc: nội dung → dự án → khách hàng → cài đặt.
 * Mục nào người dùng không có quyền sẽ tự bị ẩn.
 */
final class AdminNavigation
{
    /**
     * Mỗi nhóm: [tên nhóm, thu gọn mặc định, [lớp => [tên hiển thị, icon]]].
     *
     * @return list<array{0: string, 1: bool, 2: array<class-string, array{0: string, 1: Heroicon}>}>
     */
    private static function groups(): array
    {
        return [
            ['Nội dung', false, [
                PageResource::class => ['Trang', Heroicon::OutlinedDocumentDuplicate],
            ]],
            ['Dự án', false, [
                ProjectResource::class => ['Dự án', Heroicon::OutlinedBuildingOffice],
                ProjectCategoryResource::class => ['Danh mục dự án', Heroicon::OutlinedFolder],
            ]],
            ['Khách hàng', false, [
                ContactRequestResource::class => ['Yêu cầu tư vấn', Heroicon::OutlinedChatBubbleLeftRight],
            ]],
            ['Cài đặt', true, [
                ManageSettings::class => ['Cài đặt website', Heroicon::OutlinedCog6Tooth],
                MediaResource::class => ['Thư viện media', Heroicon::OutlinedPhoto],
                RoleResource::class => ['Vai trò & quyền', Heroicon::OutlinedShieldCheck],
            ]],
        ];
    }

    public static function build(NavigationBuilder $builder): NavigationBuilder
    {
        $groups = [];

        foreach (self::groups() as [$label, $collapsed, $entries]) {
            $items = self::items($entries);

            if ($items === []) {
                continue;
            }

            $groups[] = NavigationGroup::make($label)->items($items)->collapsed($collapsed);
        }

        return $builder
            ->items(self::items([AdminDashboard::class => ['Tổng quan', Heroicon::OutlinedHome]]))
            ->groups($groups);
    }

    /**
     * @param  array<class-string, array{0: string, 1: Heroicon}>  $entries
     * @return list<NavigationItem>
     */
    private static function items(array $entries): array
    {
        $items = [];

        foreach ($entries as $class => [$label, $icon]) {
            if (! class_exists($class) || ! $class::shouldRegisterNavigation() || ! $class::canAccess()) {
                continue;
            }

            foreach ($class::getNavigationItems() as $item) {
                $items[] = $item->label($label)->icon($icon)->activeIcon($icon);
            }
        }

        return $items;
    }
}
