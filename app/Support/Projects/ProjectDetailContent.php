<?php

namespace App\Support\Projects;

use App\Models\Project;
use App\Support\Media\MediaUrl;
use Awcodes\Curator\Models\Media;

final class ProjectDetailContent
{
    public static function make(Project $project): array
    {
        $details = $project->details ?? [];
        $galleryIds = self::mediaIds($project->gallery ?? []);
        $constructionIds = self::mediaIds(data_get($details, 'construction.images', []));
        $solutionId = data_get($details, 'solution.media_id');
        $media = Media::whereKey(array_unique([...$galleryIds, ...$constructionIds, ...(is_numeric($solutionId) ? [(int) $solutionId] : [])]))->get()->keyBy('id');
        $images = fn (array $ids) => collect($ids)->map(fn ($id) => $media->get($id))
            ->filter()->map(fn ($image) => ['url' => MediaUrl::webp($image, 1600), 'alt' => $image->alt ?: $project->title])->values()->all();
        foreach (['challenges', 'scope'] as $section) {
            $details[$section]['items'] = collect(data_get($details, $section.'.items', []))
                ->filter(fn ($item) => is_array($item) && filled($item['title'] ?? null))
                ->values()->all();
        }
        $details['solution'] ??= [];
        $details['solution']['image_url'] = is_numeric($solutionId) ? MediaUrl::webp($media->get((int) $solutionId), 1200) : null;
        $details['solution']['items'] = collect(data_get($details, 'solution.items', []))->filter(fn ($item) => filled($item['text'] ?? null))->values()->all();
        $details['gallery'] = $images($galleryIds);
        $details['construction'] ??= [];
        $details['construction']['images'] = $images($constructionIds);
        $details['overview'] ??= [];
        $details['overview']['body_html'] = (string) str((string) $project->body)->sanitizeHtml();
        $details['overview']['facts'] = collect([
            ['label' => __('Khách hàng'), 'value' => $project->client_name],
            ['label' => __('Loại dự án'), 'value' => $project->industry],
            ['label' => __('Giá trị'), 'value' => $project->price],
            ['label' => __('Bắt đầu'), 'value' => $project->started_at?->format('m/Y')],
            ['label' => __('Hoàn thành'), 'value' => $project->completed_at?->format('m/Y')],
            ['label' => __('Tình trạng'), 'value' => $project->project_status_label, 'status' => $project->project_status],
        ])->filter(fn ($item) => filled($item['value'] ?? null))->values()->all();
        $details['overview']['technologies'] = $project->technology_list;

        return $details;
    }

    private static function mediaIds(?array $items): array
    {
        return collect($items ?? [])->map(fn ($item) => is_array($item) ? ($item['id'] ?? $item['media_id'] ?? null) : $item)
            ->filter(fn ($id) => is_numeric($id))->map(fn ($id) => (int) $id)->unique()->values()->all();
    }
}
