<?php

namespace App\Filament\Pages\Schemas;

use App\Settings\CrawlSettings;
use App\Support\Seo\SeoFiles;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;

final class SeoSettingsSchema
{
    /** @return array<int, Section> */
    public static function components(): array
    {
        return [
            ...self::defaultSeoComponents(),
            ...self::crawlComponents(),
        ];
    }

    /** @return array<int, Section> */
    private static function defaultSeoComponents(): array
    {
        return [
            Section::make('SEO mặc định')
                ->icon(Heroicon::OutlinedMagnifyingGlass)
                ->description('Dùng khi một trang chưa có metadata riêng.')
                ->schema([
                    TextInput::make('seo_title')->label('SEO title')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('seo_description')->label('Meta description')->rows(3)->required()->columnSpanFull(),
                    TextInput::make('seo_keywords')->label('Từ khóa')->maxLength(500),
                    CuratorPicker::make('seo_image_media_id')->label('Ảnh Open Graph mặc định')->disk('public')->constrained()->acceptedFileTypes(['image/*']),
                ])
                ->columns(2),
        ];
    }

    /** @return array<int, Section> */
    private static function crawlComponents(): array
    {
        return [
            Section::make('robots.txt')
                ->icon(Heroicon::OutlinedCommandLine)
                ->description('Hướng dẫn công cụ tìm kiếm được phép thu thập những đường dẫn nào. File được tạo lại mỗi lần bấm Lưu cài đặt.')
                ->schema([
                    Toggle::make('robots_block_all')
                        ->label('Chặn toàn bộ công cụ tìm kiếm')
                        ->helperText('Chỉ bật khi website chưa công khai. Bật lên thì Google sẽ ngừng thu thập và sitemap bị bỏ khỏi robots.txt.')
                        ->columnSpanFull(),
                    Textarea::make('robots_disallow')
                        ->label('Đường dẫn không cho thu thập')
                        ->helperText('Mỗi dòng một đường dẫn, ví dụ: /admin hoặc /tim-kiem. Để trống để cho phép toàn bộ.')
                        ->rows(4)
                        ->maxLength(2000),
                    Textarea::make('robots_extra')
                        ->label('Dòng bổ sung (nâng cao)')
                        ->helperText('Nội dung thêm nguyên văn vào robots.txt, ví dụ User-agent riêng cho một bot.')
                        ->rows(4)
                        ->maxLength(4000),
                    Actions::make([
                        Action::make('generateRobots')
                            ->label('Tạo file robots.txt')
                            ->icon(Heroicon::OutlinedArrowPath)
                            ->color('gray')
                            ->action(fn () => self::runGenerator('seo:robots:generate', 'robots.txt')),
                        Action::make('viewRobots')
                            ->label('Xem robots.txt')
                            ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                            ->color('gray')
                            ->url(fn (): string => url('/robots.txt'), shouldOpenInNewTab: true),
                    ])->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('sitemap.xml')
                ->icon(Heroicon::OutlinedMap)
                ->description('File tĩnh public/sitemap.xml. Tự tạo lại khi nội dung công khai thay đổi, mỗi giờ một lần và mỗi lần bấm Lưu cài đặt.')
                ->schema([
                    CheckboxList::make('sitemap_sections')
                        ->label('Nội dung đưa vào sitemap')
                        ->options(CrawlSettings::SITEMAP_SECTIONS)
                        ->columns(2)
                        ->bulkToggleable()
                        ->columnSpanFull(),
                    Text::make(fn (): string => is_file(SeoFiles::sitemapPath())
                        ? 'File sitemap.xml tạo lúc '.date('H:i d/m/Y', (int) filemtime(SeoFiles::sitemapPath())).'.'
                        : 'Chưa có file sitemap.xml — bấm "Tạo lại sitemap.xml" hoặc Lưu cài đặt.')
                        ->columnSpanFull(),
                    Actions::make([
                        Action::make('generateSitemap')
                            ->label('Tạo lại sitemap.xml')
                            ->icon(Heroicon::OutlinedArrowPath)
                            ->color('gray')
                            ->action(fn () => self::runGenerator('seo:sitemap:generate', 'sitemap.xml')),
                        Action::make('viewSitemap')
                            ->label('Xem sitemap.xml')
                            ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                            ->color('gray')
                            ->url(fn (): string => url('/sitemap.xml'), shouldOpenInNewTab: true),
                    ])->columnSpanFull(),
                ]),
        ];
    }

    /** Chạy lệnh artisan tạo file tĩnh trong public, giống hệt khi chạy bằng tay hoặc lúc deploy. */
    private static function runGenerator(string $command, string $file): void
    {
        if (Artisan::call($command) !== 0) {
            Notification::make()->title('Chưa tạo được '.$file)->body('Kiểm tra quyền ghi của thư mục public.')->danger()->send();

            return;
        }

        Notification::make()->title('Đã tạo '.$file)->body('File tĩnh được tạo từ cấu hình đã lưu gần nhất.')->success()->send();
    }
}
