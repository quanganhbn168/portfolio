<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

final class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menu = Menu::query()->updateOrCreate(
            ['location' => 'header'],
            ['name' => 'Menu chính', 'is_active' => true],
        );

        $menu->items()->delete();

        foreach ([['Trang chủ', 'home'], ['Giới thiệu', 'about'], ['Liên hệ', 'contact']] as $index => [$label, $route]) {
            MenuItem::query()->create([
                'menu_id' => $menu->getKey(), 'label' => $label, 'position' => ($index + 1) * 10,
                'target' => '_self', 'url' => $route, 'linked_source_type' => 'native_route',
            ]);
        }
    }
}
