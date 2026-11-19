<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\LaravelSettings\Support\SettingsCacheFactory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (app(SettingsCacheFactory::class)->all() as $settingsCache) {
            $settingsCache->clear();
        }

        $this->call([
            WebsiteSettingsSeeder::class,
            SystemPageSeeder::class,
            MenuSeeder::class,
            ProjectCategorySeeder::class,
            ProjectSeeder::class,
        ]);

        // Tạo robots.txt và sitemap.xml tĩnh trong public từ dữ liệu vừa seed.
        $this->command?->call('seo:files:generate');
    }
}
