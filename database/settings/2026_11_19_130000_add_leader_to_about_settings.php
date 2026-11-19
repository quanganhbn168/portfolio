<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Khối "Thư của lãnh đạo" và nhãn nhỏ của khối lịch sử trên trang Giới thiệu.
        $this->migrator->add('about.leader_eyebrow', '');
        $this->migrator->add('about.leader_title', '');
        $this->migrator->add('about.leader_message', '');
        $this->migrator->add('about.leader_name', '');
        $this->migrator->add('about.leader_image_media_id', null);
        $this->migrator->add('about.leader_signature_media_id', null);
        $this->migrator->add('about.history_eyebrow', '');
    }
};
