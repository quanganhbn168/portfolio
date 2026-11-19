<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('about.default_image_media_id', NULL);
        $this->migrator->add('about.page_stats', []);
        $this->migrator->add('about.page_intro', '');
        $this->migrator->add('about.story_title', '');
        $this->migrator->add('about.story', '');
        $this->migrator->add('about.story_image_media_id', NULL);
        $this->migrator->add('about.video_source', '');
        $this->migrator->add('about.video_youtube_url', '');
        $this->migrator->add('about.video_media_id', NULL);
        $this->migrator->add('about.video_poster_media_id', NULL);
        $this->migrator->add('about.history', '');
        $this->migrator->add('about.history_title', '');
        $this->migrator->add('about.history_description', '');
        $this->migrator->add('about.history_timeline', []);
        $this->migrator->add('about.mission', '');
        $this->migrator->add('about.vision', '');
        $this->migrator->add('about.vision_title', '');
        $this->migrator->add('about.mission_title', '');
        $this->migrator->add('about.core_values_items', []);
        $this->migrator->add('about.vision_image_media_id', NULL);
        $this->migrator->add('about.mission_image_media_id', NULL);
        $this->migrator->add('about.culture_title', '');
        $this->migrator->add('about.culture_description', '');
        $this->migrator->add('about.culture_items', []);
        $this->migrator->add('about.culture_image_media_id', NULL);
        $this->migrator->add('about.principles_title', '');
        $this->migrator->add('about.services_title', '');
        $this->migrator->add('about.services_link_label', '');
        $this->migrator->add('about.stats_title', '');
        $this->migrator->add('about.office_title', '');
        $this->migrator->add('about.office_description', '');
        $this->migrator->add('about.office_image_media_id', NULL);
        $this->migrator->add('about.office_gallery', []);
        $this->migrator->add('about.hero_eyebrow', '');
        $this->migrator->add('about.hero_cta_label', '');
        $this->migrator->add('about.hero_video_label', '');
        $this->migrator->add('about.story_eyebrow', '');
        $this->migrator->add('about.vision_eyebrow', '');
        $this->migrator->add('about.mission_eyebrow', '');
        $this->migrator->add('about.values_eyebrow', '');
        $this->migrator->add('about.culture_eyebrow', '');
        $this->migrator->add('about.services_eyebrow', '');
        $this->migrator->add('about.office_eyebrow', '');
    }
};
