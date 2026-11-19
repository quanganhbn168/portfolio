<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\User;
use App\Observers\AssignNextOrderObserver;
use App\Observers\ContentSeoFallbackObserver;
use App\Observers\SitemapObserver;
use App\Observers\SlugObserver;
use App\Settings\WebsiteSettings;
use App\Support\Branding\FaviconService;
use App\Support\Media\MediaUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as BladeView;

class FrontendServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'page' => Page::class,
            'project' => Project::class,
            'project-category' => ProjectCategory::class,
            'user' => User::class,
        ]);

        foreach ([
            Page::class,
            Project::class,
        ] as $model) {
            $model::observe(SitemapObserver::class);
        }

        foreach ([
            Page::class,
            Project::class,
            ProjectCategory::class,
        ] as $model) {
            $model::observe(SlugObserver::class);
        }

        foreach ([
            Project::class,
            ProjectCategory::class,
        ] as $model) {
            $model::observe(AssignNextOrderObserver::class);
        }

        foreach ([
            Project::class,
            ProjectCategory::class,
            Page::class,
        ] as $model) {
            $model::observe(ContentSeoFallbackObserver::class);
        }

        RateLimiter::for('frontend-contact', fn ($request) => Limit::perMinute(5)->by((string) $request->ip()));

        // Console commands must be able to boot before the website database exists.
        if ($this->app->runningInConsole() && ! filter_var(env('APP_TESTING_HTTP', false), FILTER_VALIDATE_BOOL)) {
            return;
        }

        $website = app(WebsiteSettings::class);
        $mediaIds = array_values(array_filter([
            $website->logo_media_id,
            $website->seo_image_media_id,
            $website->company_profile_media_id,
            $website->about_image_media_id,
            $website->banner_media_id,
            $website->footer_background_media_id,
            $website->owner_media_id,
        ]));

        $media = $mediaIds === []
            ? collect()
            : Media::query()->whereIn('id', $mediaIds)->get()->keyBy('id');

        View::share([
            'website' => $website,
            'defaultBannerUrl' => MediaUrl::banner($media->get($website->banner_media_id)),
            'websiteMedia' => $media,
            'websiteMediaUrls' => $media->mapWithKeys(
                fn (Media $media): array => [$media->getKey() => MediaUrl::versioned($media)],
            ),
            'websiteMediaIcons' => $media->mapWithKeys(fn (Media $media): array => [
                $media->getKey() => [
                    'url' => MediaUrl::versioned($media),
                    'type' => MediaUrl::mimeType($media),
                    'sizes' => MediaUrl::iconSizes($media),
                ],
            ]),
            'faviconLinks' => app(FaviconService::class)->links(),
            'seo' => app(FrontendSeoBuilder::class)->default(),
            'footerContactPhones' => $this->phoneLinks($website),
            'footerContactBranches' => $this->contactBranches($website),
            'currentYear' => now()->year,
            'servicesLink' => filter_var($website->services_url, FILTER_VALIDATE_URL) ? [
                'url' => $website->services_url,
                'label' => filled($website->services_label) ? $website->services_label : __('Bảng giá & dịch vụ'),
                'host' => preg_replace('/^www\\./', '', (string) parse_url($website->services_url, PHP_URL_HOST)),
            ] : null,
        ]);

        View::composer('partials.footer', function (BladeView $view) use ($website): void {
            // Chính sách / điều khoản chỉ hiện khi đã tạo trang tương ứng trong mục Trang.
            $footerLegal = Page::query()
                ->where('type', Page::TYPE_CUSTOM)
                ->whereHas('slugs', fn ($query) => $query->whereIn('slug', ['chinh-sach-bao-mat', 'dieu-khoan-su-dung']))
                ->where('status', 'published')
                ->with('slugs')
                ->get()
                ->map(fn (Page $page): array => ['label' => $page->title, 'url' => $page->url])
                ->values();

            // Liên kết nhanh ở chân trang: các trang Quality / Sustainability nếu đã được xuất bản.
            $footerPages = Page::query()
                ->where('type', Page::TYPE_CUSTOM)
                ->whereHas('slugs', fn ($query) => $query->whereIn('slug', ['quality', 'sustainability']))
                ->where('status', 'published')
                ->with('slugs')
                ->orderBy('sort_order')
                ->get();

            $view->with('footerPages', $footerPages)
                ->with('footerLegal', $footerLegal)
                ->with('footerNavigation', $this->mainNavigation());
        });

        View::composer('partials.header', function (BladeView $view) use ($media, $website): void {
            $view->with([
                'headerLogoUrl' => MediaUrl::versioned($media->get($website->logo_media_id)),
                'headerNavigation' => $this->mainNavigation(),
                'headerPhones' => $this->headerPhones($website),
            ]);
        });
    }

    /** @return Collection<int, array{label: string, href: string}> */
    private function headerPhones(WebsiteSettings $website): Collection
    {
        return $this->phoneLinks($website)->take(2)->values();
    }

    /** @return Collection<int, array{label: string, href: string}> */
    private function phoneLinks(WebsiteSettings $website): Collection
    {
        $phones = collect($website->phones ?? [])
            ->filter(fn ($phone) => is_array($phone) && filled($phone['number'] ?? null));

        if ($phones->isEmpty()) {
            $phones = collect([['number' => $website->hotline], ['number' => $website->contact_phone]])
                ->filter(fn ($phone) => filled($phone['number'] ?? null));
        }

        return $phones->map(fn (array $phone): array => [
            'label' => trim($phone['number']),
            'href' => 'tel:'.preg_replace('/\s+/', '', $phone['number']),
        ])->unique('href')->values();
    }

    /** @return Collection<int, array{name: string, address: string}> */
    private function contactBranches(WebsiteSettings $website): Collection
    {
        return collect($website->branches ?? [])
            ->filter(fn ($branch): bool => is_array($branch)
                && ($branch['is_active'] ?? true)
                && filled($branch['address'] ?? null))
            ->map(fn (array $branch): array => [
                'name' => (string) ($branch['name'] ?? __('Địa chỉ')),
                'address' => trim((string) $branch['address']),
            ])
            ->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    private function mainNavigation(): Collection
    {
        return collect([
            [__('Trang chủ'), route('home'), request()->routeIs('home', 'slug.show')],
            [__('Giới thiệu'), route('about'), request()->routeIs('about')],
            [__('Liên hệ'), route('contact'), request()->routeIs('contact')],
        ])->map(fn (array $item): array => [
            'label' => $item[0],
            'url' => $item[1],
            'target' => '_self',
            'is_active' => $item[2],
            'has_children' => false,
            'children' => collect(),
        ]);
    }
}
