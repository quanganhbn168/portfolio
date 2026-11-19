<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AboutSettings extends Settings
{
    public ?int $default_image_media_id = null;

    public array $page_stats = [];

    public string $page_intro = '';

    public string $story_title = '';

    public string $story = '';

    public ?int $story_image_media_id = null;

    public string $video_source = '';

    public string $video_youtube_url = '';

    public ?int $video_media_id = null;

    public ?int $video_poster_media_id = null;

    public string $history = '';

    public string $history_title = '';

    public string $history_description = '';

    public array $history_timeline = [];

    public string $mission = '';

    public string $vision = '';

    public string $vision_title = '';

    public string $mission_title = '';

    public array $core_values_items = [];

    public ?int $vision_image_media_id = null;

    public ?int $mission_image_media_id = null;

    public string $culture_title = '';

    public string $culture_description = '';

    public array $culture_items = [];

    public ?int $culture_image_media_id = null;

    public string $principles_title = '';

    public string $services_title = '';

    public string $services_link_label = '';

    public string $stats_title = '';

    public string $office_title = '';

    public string $office_description = '';

    public ?int $office_image_media_id = null;

    public array $office_gallery = [];

    public string $hero_eyebrow = '';

    public string $hero_cta_label = '';

    public string $hero_video_label = '';

    public string $story_eyebrow = '';

    public string $vision_eyebrow = '';

    public string $mission_eyebrow = '';

    public string $values_eyebrow = '';

    public string $culture_eyebrow = '';

    public string $services_eyebrow = '';

    public string $office_eyebrow = '';

    public string $leader_eyebrow = '';

    public string $leader_title = '';

    public string $leader_message = '';

    public string $leader_name = '';

    public ?int $leader_image_media_id = null;

    public ?int $leader_signature_media_id = null;

    public string $history_eyebrow = '';

    public static function group(): string
    {
        return 'about';
    }
}
