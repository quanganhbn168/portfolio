<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Dịch vụ cung cấp (hiện thành thẻ ở trang chủ và trang Dịch vụ & bảng giá). */
class Service extends Model
{
    use HasFactory;

    /** Icon Font Awesome có thể chọn trong admin. */
    public const ICONS = [
        'fa-code' => 'Lập trình web',
        'fa-pen-ruler' => 'Thiết kế giao diện',
        'fa-rocket' => 'Landing page',
        'fa-cart-shopping' => 'Thương mại điện tử',
        'fa-gears' => 'Phần mềm quản lý',
        'fa-mobile-screen' => 'Ứng dụng di động',
        'fa-magnifying-glass-chart' => 'SEO',
        'fa-bullhorn' => 'Marketing',
        'fa-server' => 'Hosting & vận hành',
        'fa-shield-halved' => 'Bảo mật',
        'fa-database' => 'Dữ liệu',
        'fa-robot' => 'Tích hợp AI',
        'fa-headset' => 'Hỗ trợ & bảo trì',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
