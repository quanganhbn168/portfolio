<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\PreservesUnchangedSettings;
use App\Filament\Pages\Schemas\SettingsForm;
use App\Settings\AboutSettings;
use App\Settings\CompanySettings;
use App\Settings\CrawlSettings;
use App\Settings\WebsiteSettings;
use App\Support\Branding\FaviconService;
use App\Support\Maps\GoogleMapsShareResolver;
use App\Support\Maps\GoogleMapsUrl;
use App\Support\Seo\SeoFiles;
use Awcodes\Curator\Models\Media;
use BackedEnum;
use ErrorException;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use UnitEnum;

class ManageSettings extends Page
{
    use InteractsWithFormActions;
    use PreservesUnchangedSettings;

    protected static ?string $slug = 'settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Cài đặt website';

    protected static ?string $title = 'Cài đặt website';

    protected static string|UnitEnum|null $navigationGroup = 'Cài đặt website';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(
        WebsiteSettings $website,
        CompanySettings $company,
        AboutSettings $about,
        CrawlSettings $crawl,
    ): void {
        $this->fillSettingsForm([
            'site_name' => $website->site_name,
            'tagline' => $website->tagline,
            'footer_description' => $website->footer_description,
            'logo_media_id' => $website->logo_media_id,
            'favicon_media_id' => $website->favicon_media_id,
            'contact_email' => $website->contact_email,
            'hotline' => $website->hotline,
            'contact_phone' => $website->contact_phone,
            'address' => $website->address,
            'phones' => $this->contactPhonesForForm($website),
            'branches' => $this->contactBranchesForForm($website),
            'google_maps_embed_url' => $website->google_maps_embed_url,
            'google_maps_url' => $website->google_maps_url,
            'facebook_url' => $website->facebook_url,
            'zalo_url' => $website->zalo_url,
            'youtube_url' => $website->youtube_url,
            'services_url' => $website->services_url,
            'services_label' => $website->services_label,
            'seo_title' => $website->seo_title,
            'seo_description' => $website->seo_description,
            'seo_keywords' => $website->seo_keywords,
            'seo_image_media_id' => $website->seo_image_media_id,
            'footer_background_media_id' => $website->footer_background_media_id,
            'banner_media_id' => $website->banner_media_id,
            'company_name' => $website->company_name,
            'company_profile_media_id' => $website->company_profile_media_id,
            'tax_code' => $company->tax_code,
            'representative' => $company->representative,
            'founded_year' => $company->founded_year,
            'business_license' => $company->business_license,
            'about_image_media_id' => $website->about_image_media_id,
            'hero_cta_label' => $about->hero_cta_label,
            'hero_video_label' => $about->hero_video_label,
            'vision_title' => $about->vision_title,
            'mission_title' => $about->mission_title,
            'page_intro' => $about->page_intro,
            'story_title' => $about->story_title,
            'story' => $about->story,
            'page_stats' => $about->page_stats,
            'default_image_media_id' => $about->default_image_media_id,
            'story_image_media_id' => $about->story_image_media_id,
            'video_source' => $about->video_source,
            'video_youtube_url' => $about->video_youtube_url,
            'video_media_id' => $about->video_media_id,
            'video_poster_media_id' => $about->video_poster_media_id,
            'history' => $about->history,
            'history_title' => $about->history_title,
            'history_description' => $about->history_description,
            'history_timeline' => $about->history_timeline,
            'mission' => $about->mission,
            'vision' => $about->vision,
            'core_values_items' => $about->core_values_items,
            'vision_image_media_id' => $about->vision_image_media_id,
            'mission_image_media_id' => $about->mission_image_media_id,
            'culture_title' => $about->culture_title,
            'culture_description' => $about->culture_description,
            'culture_items' => $about->culture_items,
            'culture_image_media_id' => $about->culture_image_media_id,
            'principles_title' => $about->principles_title,
            'services_link_label' => $about->services_link_label,
            'stats_title' => $about->stats_title,
            'office_title' => $about->office_title,
            'office_description' => $about->office_description,
            'office_gallery' => $about->office_gallery,
            'robots_block_all' => $crawl->robots_block_all,
            'robots_disallow' => $crawl->robots_disallow,
            'robots_extra' => $crawl->robots_extra,
            'sitemap_sections' => $crawl->sitemap_sections,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return SettingsForm::configure($schema);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make($this->getFormActions())
                    ->alignment($this->getFormActionsAlignment())
                    ->fullWidth($this->hasFullWidthFormActions())
                    ->key('form-actions'),
            ]);
    }

    public function save(
        WebsiteSettings $website,
        CompanySettings $company,
        AboutSettings $about,
        FaviconService $favicons,
        GoogleMapsShareResolver $maps,
        CrawlSettings $crawl,
        SeoFiles $seoFiles,
    ): void {
        $data = $this->settingsFormData();

        $this->saveWebsite($website, $data, $favicons, $maps);
        $this->saveAboutImage($data);
        $this->saveCompany($company, $data);
        $this->saveAbout($about, $data);
        $this->saveCrawl($crawl, $data);
        $seoFileError = $seoFiles->writeAll();

        app()->call([$this, 'mount']);

        Notification::make()
            ->title('Đã lưu cài đặt website')
            ->success()
            ->send();

        if ($seoFileError !== null) {
            Notification::make()
                ->title('Cài đặt đã lưu nhưng chưa cập nhật được robots.txt / sitemap.xml')
                ->body($seoFileError)
                ->warning()
                ->persistent()
                ->send();
        }
    }

    /** @return array<int, Action> */
    protected function getFormActions(): array
    {
        return [
            Action::make('discard')->label('Hủy thay đổi')->color('gray')->action(fn () => app()->call([$this, 'mount'])),
            Action::make('save')
                ->label('Lưu cài đặt')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    /** @param array<string, mixed> $data */
    private function saveWebsite(WebsiteSettings $website, array $data, FaviconService $favicons, GoogleMapsShareResolver $maps): void
    {
        $faviconId = filled($data['favicon_media_id'] ?? null) ? (int) $data['favicon_media_id'] : null;
        if ($faviconId !== $website->favicon_media_id && $faviconId !== null) {
            try {
                $media = Media::query()->find($faviconId);
                if (! $media) {
                    throw new RuntimeException('Không tìm thấy favicon đã chọn trong kho media.');
                }
                $favicons->sync($media);
            } catch (RuntimeException|ErrorException $exception) {
                report($exception);
                throw ValidationException::withMessages([
                    'data.favicon_media_id' => 'Chưa lưu được favicon. Kiểm tra file nguồn và quyền ghi bộ favicon trong public, sau đó lưu lại.',
                ]);
            }
        }

        foreach (['site_name', 'tagline', 'footer_description', 'contact_email', 'facebook_url', 'zalo_url', 'youtube_url', 'services_url', 'services_label', 'seo_title', 'seo_description', 'seo_keywords'] as $key) {
            $website->{$key} = (string) ($data[$key] ?? '');
        }

        $website->phones = $this->normalizeContactPhones($data['phones'] ?? []);
        $website->hotline = $website->phones[0]['number'] ?? '';
        $website->contact_phone = $website->phones[1]['number'] ?? '';
        $website->branches = $this->normalizeContactBranches($data['branches'] ?? []);
        $website->address = collect($website->branches)->firstWhere('is_active', true)['address']
            ?? ($website->branches[0]['address'] ?? '');

        $website->google_maps_url = filled($data['google_maps_url'] ?? null)
            ? trim((string) $data['google_maps_url'])
            : null;
        $website->google_maps_embed_url = GoogleMapsUrl::normalizeEmbed($data['google_maps_embed_url'] ?? null)
            ?? $maps->resolveEmbed($website->google_maps_url);

        foreach (['logo_media_id', 'favicon_media_id', 'seo_image_media_id', 'footer_background_media_id', 'banner_media_id'] as $key) {
            $website->{$key} = filled($data[$key] ?? null) ? (int) $data[$key] : null;
        }

        $website->save();
    }

    /** @return list<array{title: string, description: string}> */
    private function contentItems(mixed $items): array
    {
        return collect(is_array($items) ? $items : [])
            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['title'] ?? null))
            ->map(fn (array $item): array => [
                'title' => trim((string) $item['title']),
                'description' => trim((string) ($item['description'] ?? '')),
            ] + (filled($item['icon'] ?? null) ? ['icon' => (string) $item['icon']] : []))
            ->values()
            ->all();
    }

    private function contactPhonesForForm(WebsiteSettings $website): array
    {
        $phones = is_array($website->phones ?? null) ? $website->phones : [];

        if ($phones !== []) {
            return $this->normalizeContactPhones($phones);
        }

        return array_values(array_filter([
            filled($website->hotline) ? ['label' => 'Hotline chính', 'number' => $website->hotline, 'is_primary' => true] : null,
            filled($website->contact_phone) ? ['label' => 'Số điện thoại phụ', 'number' => $website->contact_phone, 'is_primary' => false] : null,
        ]));
    }

    private function contactBranchesForForm(WebsiteSettings $website): array
    {
        $branches = is_array($website->branches ?? null) ? $website->branches : [];

        if ($branches !== []) {
            return $this->normalizeContactBranches($branches);
        }

        return filled($website->address)
            ? [['name' => 'Trụ sở chính', 'address' => $website->address, 'is_active' => true]]
            : [];
    }

    private function normalizeContactPhones(mixed $phones): array
    {
        $normalized = array_values(array_filter(array_map(
            static function (mixed $phone): ?array {
                if (! is_array($phone)) {
                    return null;
                }

                $number = trim((string) ($phone['number'] ?? ''));

                return $number === '' ? null : [
                    'label' => trim((string) ($phone['label'] ?? '')),
                    'number' => $number,
                    'is_primary' => (bool) ($phone['is_primary'] ?? false),
                ];
            },
            is_array($phones) ? $phones : [],
        )));

        if ($normalized !== []) {
            $primaryIndex = collect($normalized)->search(fn (array $phone): bool => $phone['is_primary']);
            $primaryIndex = $primaryIndex === false ? 0 : $primaryIndex;

            foreach ($normalized as $index => &$phone) {
                $phone['is_primary'] = $index === $primaryIndex;
            }
            unset($phone);
        }

        return $normalized;
    }

    private function normalizeContactBranches(mixed $branches): array
    {
        return array_values(array_filter(array_map(
            static function (mixed $branch): ?array {
                if (! is_array($branch)) {
                    return null;
                }

                $address = trim((string) ($branch['address'] ?? ''));

                return $address === '' ? null : [
                    'name' => trim((string) ($branch['name'] ?? 'Địa điểm')),
                    'address' => $address,
                    'phone' => trim((string) ($branch['phone'] ?? '')),
                    'map_url' => trim((string) ($branch['map_url'] ?? '')),
                    'is_active' => (bool) ($branch['is_active'] ?? true),
                ];
            },
            is_array($branches) ? $branches : [],
        )));
    }

    /** @param array<string, mixed> $data */
    private function saveAboutImage(array $data): void
    {
        $website = app(WebsiteSettings::class);
        $website->about_image_media_id = filled($data['about_image_media_id'] ?? null)
            ? (int) $data['about_image_media_id']
            : null;
        $website->save();
    }

    /** @param array<string, mixed> $data */
    private function saveCompany(CompanySettings $company, array $data): void
    {
        $company->tax_code = (string) ($data['tax_code'] ?? '');
        $company->representative = (string) ($data['representative'] ?? '');
        $company->founded_year = filled($data['founded_year'] ?? null) ? (int) $data['founded_year'] : null;
        $company->business_license = (string) ($data['business_license'] ?? '');
        $company->save();

        $website = app(WebsiteSettings::class);
        $website->company_name = (string) ($data['company_name'] ?? '');
        $website->company_profile_media_id = filled($data['company_profile_media_id'] ?? null)
            ? (int) $data['company_profile_media_id']
            : null;
        $website->save();
    }

    /** @param array<string, mixed> $data */
    private function saveAbout(AboutSettings $about, array $data): void
    {
        foreach (['hero_cta_label', 'hero_video_label', 'vision_title', 'mission_title', 'page_intro', 'story_title', 'story', 'history', 'history_title', 'history_description', 'mission', 'vision', 'culture_title', 'culture_description', 'principles_title', 'services_link_label', 'stats_title', 'office_title', 'office_description'] as $key) {
            $about->{$key} = trim((string) ($data[$key] ?? ''));
        }

        $about->page_stats = is_array($data['page_stats'] ?? null) ? $data['page_stats'] : [];
        $about->core_values_items = self::iconItems($data['core_values_items'] ?? []);
        $about->culture_items = self::iconItems($data['culture_items'] ?? []);
        $about->history_timeline = is_array($data['history_timeline'] ?? null) ? $data['history_timeline'] : [];

        $about->video_source = in_array($data['video_source'] ?? null, ['youtube', 'upload'], true)
            ? $data['video_source']
            : '';
        $about->video_youtube_url = trim((string) ($data['video_youtube_url'] ?? ''));
        $about->video_media_id = filled($data['video_media_id'] ?? null)
            ? (int) $data['video_media_id']
            : null;

        foreach (['default_image_media_id', 'story_image_media_id', 'video_poster_media_id', 'vision_image_media_id', 'mission_image_media_id', 'culture_image_media_id'] as $key) {
            $about->{$key} = filled($data[$key] ?? null) ? (int) $data[$key] : null;
        }

        $about->office_gallery = collect($data['office_gallery'] ?? [])
            ->filter(fn (mixed $mediaId): bool => is_numeric($mediaId))
            ->map(fn (mixed $mediaId): int => (int) $mediaId)
            ->unique()
            ->values()
            ->all();
        $about->office_image_media_id = $about->office_gallery[0] ?? null;

        $about->save();
    }

    /** @return array<int, array{icon: string, title: string, description: string}> */
    private static function iconItems(mixed $items): array
    {
        return collect(is_array($items) ? $items : [])
            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['title'] ?? null))
            ->map(fn (array $item): array => [
                'icon' => (string) ($item['icon'] ?? ''),
                'title' => trim((string) $item['title']),
                'description' => trim((string) ($item['description'] ?? '')),
            ])
            ->values()
            ->all();
    }

    /** @param array<string, mixed> $data */
    private function saveCrawl(CrawlSettings $crawl, array $data): void
    {
        $crawl->robots_block_all = (bool) ($data['robots_block_all'] ?? false);
        $crawl->robots_disallow = trim((string) ($data['robots_disallow'] ?? ''));
        $crawl->robots_extra = trim((string) ($data['robots_extra'] ?? ''));
        $crawl->sitemap_sections = collect($data['sitemap_sections'] ?? [])
            ->filter(fn (mixed $section): bool => is_string($section) && array_key_exists($section, CrawlSettings::SITEMAP_SECTIONS))
            ->values()
            ->all();
        $crawl->save();
    }
}
