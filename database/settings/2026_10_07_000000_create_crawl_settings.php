<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $defaults = [
            'robots_block_all' => false,
            'robots_disallow' => '',
            'robots_extra' => '',
            'sitemap_sections' => ['pages', 'site_pages', 'capabilities', 'solutions', 'projects', 'posts', 'products', 'product_categories', 'intros', 'recruitment'],
        ];

        foreach ($defaults as $name => $value) {
            if (! $this->migrator->exists('crawl.'.$name)) {
                $this->migrator->add('crawl.'.$name, $value);
            }
        }
    }
};
