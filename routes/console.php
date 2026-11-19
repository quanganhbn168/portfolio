<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Bài hẹn giờ xuất bản tự hiện ra mà không cần ai lưu lại, nên sitemap tĩnh được làm mới theo giờ.
Schedule::command('seo:sitemap:generate')->hourly()->withoutOverlapping();
