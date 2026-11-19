<?php

namespace Tests;

use Database\Seeders\SystemPageSeeder;
use Database\Seeders\WebsiteSettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Các test hiện có kiểm tra nội dung tiếng Việt; ngôn ngữ mặc định của website là tiếng Anh.
        config(['app.locale' => 'vi']);
        app()->setLocale('vi');

        if (! in_array(RefreshDatabase::class, class_uses_recursive($this), true)) {
            return;
        }

        $this->seed([
            WebsiteSettingsSeeder::class,
            SystemPageSeeder::class,
        ]);
    }
}
