<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('website.site_name', '');
        $this->migrator->add('website.tagline', '');
        $this->migrator->add('website.footer_description', '');
        $this->migrator->add('website.company_name', '');
        $this->migrator->add('website.contact_email', '');
        $this->migrator->add('website.hotline', '');
        $this->migrator->add('website.contact_phone', '');
        $this->migrator->add('website.address', '');
        $this->migrator->add('website.facebook_url', '');
        $this->migrator->add('website.zalo_url', '');
        $this->migrator->add('website.youtube_url', '');
        $this->migrator->add('website.owner_name', '');
        $this->migrator->add('website.owner_title', '');
        $this->migrator->add('website.owner_bio', '');
        $this->migrator->add('website.owner_media_id', null);
        $this->migrator->add('website.owner_cover_media_id', null);
        $this->migrator->add('website.owner_workplace', '');
        $this->migrator->add('website.owner_location', '');
        $this->migrator->add('website.github_url', '');
        $this->migrator->add('website.tiktok_url', '');
        $this->migrator->add('website.telegram_url', '');
        $this->migrator->add('website.seo_title', '');
        $this->migrator->add('website.seo_description', '');
        $this->migrator->add('website.seo_keywords', '');
        $this->migrator->add('website.logo_media_id', NULL);
        $this->migrator->add('website.favicon_media_id', NULL);
        $this->migrator->add('website.seo_image_media_id', NULL);
        $this->migrator->add('website.company_profile_media_id', NULL);
        $this->migrator->add('website.about_image_media_id', NULL);
        $this->migrator->add('website.banner_media_id', NULL);
        $this->migrator->add('website.footer_background_media_id', NULL);
        $this->migrator->add('website.google_maps_embed_url', NULL);
        $this->migrator->add('website.google_maps_url', NULL);
        $this->migrator->add('website.phones', []);
        $this->migrator->add('website.branches', []);
    }
};
