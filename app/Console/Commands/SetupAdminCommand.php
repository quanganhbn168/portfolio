<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;

class SetupAdminCommand extends Command
{
    protected $signature = 'app:setup-admin
        {--email=admin@example.com : Email đăng nhập}
        {--password=admin123 : Mật khẩu (chỉ đặt khi tạo mới user)}
        {--name=Quản trị viên : Tên hiển thị}';

    protected $description = 'Sinh quyền Filament Shield, tạo tài khoản quản trị mặc định và gán vai trò super_admin. Chạy lại nhiều lần vẫn an toàn.';

    public function handle(): int
    {
        $this->components->task('Sinh quyền cho panel admin', fn () => Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'admin',
            '--option' => 'permissions',
            '--no-interaction' => true,
        ]) === self::SUCCESS);

        $email = (string) $this->option('email');
        $user = User::query()->where('email', $email)->first();
        $created = $user === null;

        if ($created) {
            $user = User::query()->create([
                'name' => (string) $this->option('name'),
                'email' => $email,
                'password' => (string) $this->option('password'),
            ]);
        }

        Role::findOrCreate(config('filament-shield.super_admin.name', 'super_admin'), 'web');
        $user->assignRole(config('filament-shield.super_admin.name', 'super_admin'));

        $this->components->info($created
            ? "Đã tạo {$email} và gán super_admin. Hãy đổi email và mật khẩu trong trang Hồ sơ trước khi bàn giao."
            : "{$email} đã tồn tại, giữ nguyên mật khẩu, đã đảm bảo có vai trò super_admin.");

        return self::SUCCESS;
    }
}
