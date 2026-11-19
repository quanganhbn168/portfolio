<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Settings\AboutSettings;
use App\Settings\WebsiteSettings;
use App\Support\Media\MediaUrl as CuratorMediaUrl;
use App\Support\Pages\SystemPageProfileResolver;
use App\Support\Seo\FrontendSeoBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly AboutSettings $settings,
        private readonly WebsiteSettings $website,
        private readonly SystemPageProfileResolver $systemPages,
    ) {}

    public function __invoke(): View
    {
        $page = $this->systemPages->require('about');
        $managed = [
            'title' => $page['title'],
            'hero_cta_label' => trim($this->settings->hero_cta_label),
            'hero_video_label' => trim($this->settings->hero_video_label),
            'vision_title' => trim($this->settings->vision_title),
            'mission_title' => trim($this->settings->mission_title),
            'intro' => trim($this->settings->page_intro),
            'story_eyebrow' => trim($this->settings->story_eyebrow),
            'story_title' => trim($this->settings->story_title),
            'leader_eyebrow' => trim($this->settings->leader_eyebrow),
            'leader_title' => trim($this->settings->leader_title),
            'leader_message' => (string) str((string) $this->settings->leader_message)->sanitizeHtml(),
            'leader_name' => trim($this->settings->leader_name),
            'history_eyebrow' => trim($this->settings->history_eyebrow),
            'story' => (string) str((string) $this->settings->story)->sanitizeHtml(),
            'history' => $this->settings->history,
            'history_title' => trim($this->settings->history_title),
            'history_description' => trim($this->settings->history_description),
            'mission' => trim($this->settings->mission),
            'vision' => trim($this->settings->vision),
            'culture_title' => trim($this->settings->culture_title),
            'culture_description' => trim($this->settings->culture_description),
            'principles_title' => trim($this->settings->principles_title),
            'services_link_label' => trim($this->settings->services_link_label),
            'stats_title' => trim($this->settings->stats_title),
            'office_title' => trim($this->settings->office_title),
            'office_description' => trim($this->settings->office_description),
        ];
        $historyTimeline = $this->historyTimeline($this->settings->history_timeline ?? []);
        $hasManagedContent = collect($managed)->filter(fn (string $value): bool => filled($value))->isNotEmpty()
            || $historyTimeline->isNotEmpty();

        abort_if(! $hasManagedContent, 404);

        $officeGalleryIds = collect($this->settings->office_gallery ?? [])
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();
        $mediaIds = collect([
            $this->settings->default_image_media_id,
            $this->settings->story_image_media_id,
            $this->settings->video_poster_media_id,
            $this->settings->vision_image_media_id,
            $this->settings->mission_image_media_id,
            $this->settings->culture_image_media_id,
            $this->settings->office_image_media_id,
            $this->settings->leader_image_media_id,
            $this->settings->leader_signature_media_id,
        ])->merge($officeGalleryIds)
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();
        $media = $mediaIds->isEmpty()
            ? collect()
            : Media::query()->whereIn('id', $mediaIds)->get()->keyBy('id');
        $fallbackImageUrl = $this->sectionImageUrl($media, $this->settings->default_image_media_id);
        $about = $managed;
        $about['title'] = $managed['title'] ?: $this->website->company_name;
        $about['image_url'] = $fallbackImageUrl;
        $about['story_image_url'] = $this->sectionImageUrl($media, $this->settings->story_image_media_id);
        $about['vision_image_url'] = $this->sectionImageUrl($media, $this->settings->vision_image_media_id, $fallbackImageUrl);
        $about['mission_image_url'] = $this->sectionImageUrl($media, $this->settings->mission_image_media_id, $fallbackImageUrl);
        $about['culture_image_url'] = $this->sectionImageUrl($media, $this->settings->culture_image_media_id);
        $about['leader_image_url'] = $this->sectionImageUrl($media, $this->settings->leader_image_media_id);
        $signature = $media->get((int) $this->settings->leader_signature_media_id);
        $about['leader_signature_url'] = $signature && str_starts_with((string) $signature->type, 'image/') ? CuratorMediaUrl::webp($signature, 640, 90) : null;
        $about['stats'] = collect($this->settings->page_stats ?? [])
            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['value'] ?? null))
            ->map(fn (array $item): array => [
                'icon' => $this->icon($item['icon'] ?? null),
                'value' => trim((string) $item['value']),
                'label' => trim((string) ($item['label'] ?? '')),
            ])->values()->all();
        $about['values'] = $this->iconItems($this->settings->core_values_items);
        $about['culture_items'] = $this->iconItems($this->settings->culture_items);
        // Không lấy ảnh hero làm dự phòng: khối văn phòng chỉ hiện khi đã chọn ảnh/gallery riêng.
        $about['office_image_url'] = $this->sectionImageUrl($media, $this->settings->office_image_media_id);
        $about['office_gallery'] = $officeGalleryIds
            ->map(function (int $mediaId, int $index) use ($media, $managed): ?array {
                $item = $media->get($mediaId);
                $url = $item && str_starts_with((string) $item->type, 'image/')
                    ? CuratorMediaUrl::webp($item, 1600)
                    : null;

                return $url ? [
                    'url' => $url,
                    'alt' => trim((string) ($item->alt ?? $item->title ?? '')) ?: ($managed['office_title'] ?: __('Văn phòng :name', ['name' => $this->website->site_name])).' — '.__('ảnh :number', ['number' => $index + 1]),
                ] : null;
            })
            ->filter()
            ->values();
        if ($about['office_gallery']->isEmpty() && $about['office_image_url']) {
            $about['office_gallery'] = collect([[
                'url' => $about['office_image_url'],
                'alt' => $managed['office_title'] ?: __('Văn phòng :name', ['name' => $this->website->site_name]),
            ]]);
        }
        $videoPosterUrl = $this->sectionImageUrl($media, $this->settings->video_poster_media_id, $fallbackImageUrl);
        $about['video'] = $this->introVideo($videoPosterUrl);

        return view('frontend.about', compact('about') + [
            'historyTimeline' => $historyTimeline,
            'page' => $page,
            'pageBannerUrl' => $page['banner_url'],
            'seo' => $this->seo->systemPage($page, 'about'),
        ]);
    }

    /** @param array<int, mixed> $items */
    private function historyTimeline(array $items): Collection
    {
        $items = collect($items)
            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['year'] ?? null))
            ->values();
        $mediaIds = $items
            ->map(fn (array $item): mixed => $item['media_id'] ?? null)
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();
        $media = $mediaIds->isEmpty()
            ? collect()
            : Media::query()->whereIn('id', $mediaIds)->get()->keyBy('id');

        return $items
            ->map(function (array $item, int $index) use ($media): array {
                $title = $this->textValue($item['title'] ?? null);
                $description = $this->textValue($item['description'] ?? null);
                $mediaId = is_numeric($item['media_id'] ?? null) ? (int) $item['media_id'] : null;

                return [
                    'id' => 'history-'.$index,
                    'year' => trim((string) $item['year']),
                    'title' => $title,
                    'description' => $description,
                    'icon' => $this->icon($item['icon'] ?? null, 'fa-industry'),
                    'image_url' => CuratorMediaUrl::webp($media->get($mediaId), 900),
                ];
            })
            ->filter(fn (array $item): bool => filled($item['title']) || filled($item['description']))
            ->values();
    }

    /** @return array<int, array{icon: string, title: string, description: string}> */
    private function iconItems(array $items): array
    {
        return collect($items)
            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['title'] ?? null))
            ->map(fn (array $item): array => [
                'icon' => $this->icon($item['icon'] ?? null),
                'title' => trim((string) $item['title']),
                'description' => trim((string) ($item['description'] ?? '')),
            ])
            ->values()
            ->all();
    }

    /** Chỉ nhận class Font Awesome dạng fa-xxx để không in ra class lạ. */
    private function icon(mixed $icon, string $default = 'fa-circle-check'): string
    {
        return preg_match('/^fa-[a-z0-9-]+$/', (string) $icon) === 1 ? (string) $icon : $default;
    }

    private function textValue(mixed $value): string
    {
        return trim((string) $value);
    }

    /** @param Collection<int, Media> $media */
    private function sectionImageUrl(Collection $media, mixed $mediaId, ?string $fallback = null): ?string
    {
        $item = is_numeric($mediaId) ? $media->get((int) $mediaId) : null;

        return $item && str_starts_with((string) $item->type, 'image/')
            ? CuratorMediaUrl::webp($item, 1600)
            : $fallback;
    }

    /** @return array{source: string, url: string, poster_url: ?string}|null */
    private function introVideo(?string $posterUrl): ?array
    {
        if ($this->settings->video_source === 'youtube') {
            $embedUrl = $this->youtubeEmbedUrl($this->settings->video_youtube_url);

            return $embedUrl ? [
                'source' => 'youtube',
                'url' => $embedUrl,
                'poster_url' => $posterUrl,
            ] : null;
        }

        if ($this->settings->video_source !== 'upload' || ! $this->settings->video_media_id) {
            return null;
        }

        $media = Media::query()->find($this->settings->video_media_id);
        $videoUrl = $media && str_starts_with((string) $media->type, 'video/')
            ? CuratorMediaUrl::versioned($media)
            : null;

        return $videoUrl ? [
            'source' => 'upload',
            'url' => $videoUrl,
            'poster_url' => $posterUrl,
        ] : null;
    }

    private function youtubeEmbedUrl(?string $url): ?string
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        $videoId = null;

        if ($host === 'youtu.be') {
            $videoId = explode('/', $path)[0] ?? null;
        } elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com'], true)) {
            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
            $segments = explode('/', $path);
            $videoId = $path === 'watch'
                ? ($query['v'] ?? null)
                : (in_array($segments[0] ?? null, ['embed', 'shorts', 'live'], true) ? ($segments[1] ?? null) : null);
        }

        if (! is_string($videoId) || preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId) !== 1) {
            return null;
        }

        return 'https://www.youtube-nocookie.com/embed/'.$videoId.'?rel=0';
    }
}
