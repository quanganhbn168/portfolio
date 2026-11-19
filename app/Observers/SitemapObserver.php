<?php

namespace App\Observers;

use App\Support\Seo\SeoFiles;
use Illuminate\Database\Eloquent\Model;

/** Nội dung công khai đổi thì sitemap.xml tĩnh được tạo lại sau khi phản hồi đã gửi đi. */
class SitemapObserver
{
    private const QUEUED = 'seo.sitemap.refresh-queued';

    public function saved(Model $model): void
    {
        $this->queueRefresh();
    }

    public function deleted(Model $model): void
    {
        $this->queueRefresh();
    }

    /** Nhiều thay đổi trong cùng một request chỉ tạo lại sitemap một lần. */
    private function queueRefresh(): void
    {
        if (app()->bound(self::QUEUED)) {
            return;
        }

        app()->instance(self::QUEUED, true);

        dispatch(function (): void {
            app()->forgetInstance(self::QUEUED);
            app(SeoFiles::class)->refreshSitemap();
        })->afterResponse();
    }
}
