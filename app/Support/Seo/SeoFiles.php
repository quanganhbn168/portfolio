<?php

namespace App\Support\Seo;

use Throwable;

/** Ghi robots.txt và sitemap.xml thành file tĩnh trong public. */
final class SeoFiles
{
    public function __construct(
        private readonly SitemapBuilder $sitemap,
        private readonly RobotsTxt $robots,
    ) {}

    public static function basePath(): string
    {
        return rtrim((string) (config('seo.files_path') ?: public_path()), '/'.DIRECTORY_SEPARATOR);
    }

    public static function sitemapPath(): string
    {
        return self::basePath().DIRECTORY_SEPARATOR.'sitemap.xml';
    }

    public static function robotsPath(): string
    {
        return self::basePath().DIRECTORY_SEPARATOR.'robots.txt';
    }

    public function writeSitemap(): string
    {
        $this->sitemap->build()->writeToFile(self::sitemapPath());

        return self::sitemapPath();
    }

    public function writeRobots(): string
    {
        file_put_contents(self::robotsPath(), $this->robots->render());

        return self::robotsPath();
    }

    /**
     * Ghi cả robots.txt và sitemap.xml. Không ném lỗi: trả về thông báo lỗi (null nếu thành công)
     * để nơi gọi báo cho người dùng thay vì làm hỏng thao tác lưu cài đặt.
     */
    public function writeAll(): ?string
    {
        try {
            $this->writeRobots();
            $this->writeSitemap();

            return null;
        } catch (Throwable $exception) {
            report($exception);

            return 'Không ghi được robots.txt hoặc sitemap.xml vào thư mục '.self::basePath()
                .'. Hãy cấp quyền ghi cho user chạy web (php-fpm) trên thư mục và hai file này.';
        }
    }

    /** Làm mới sitemap sau khi nội dung đổi; lỗi ghi file không được làm hỏng thao tác lưu. */
    public function refreshSitemap(): void
    {
        try {
            $this->writeSitemap();
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
