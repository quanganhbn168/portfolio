<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

/** Dịch vụ mẫu: sửa trong admin → Dịch vụ & bảng giá → Dịch vụ. */
final class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['Thiết kế website doanh nghiệp', 'fa-code', 'Website giới thiệu công ty, chuẩn SEO, responsive, có trang quản trị để tự cập nhật nội dung.'],
            ['Landing page bán hàng', 'fa-rocket', 'Trang đích tối ưu chuyển đổi cho chiến dịch quảng cáo, tải nhanh và dễ đo lường.'],
            ['Website bán hàng', 'fa-cart-shopping', 'Cửa hàng online quản lý sản phẩm, đơn hàng, thanh toán và báo cáo doanh thu.'],
            ['Phần mềm quản lý', 'fa-gears', 'Phần mềm theo yêu cầu: bán hàng, kho, khách hàng, báo cáo, phân quyền người dùng.'],
            ['Tối ưu SEO & tốc độ', 'fa-magnifying-glass-chart', 'Cấu trúc chuẩn Google, tối ưu tốc độ tải và nội dung để lên top tìm kiếm.'],
            ['Hosting, bảo trì & hỗ trợ', 'fa-headset', 'Cài đặt máy chủ, sao lưu, cập nhật bảo mật và hỗ trợ kỹ thuật sau bàn giao.'],
        ] as $index => [$title, $icon, $description]) {
            Service::query()->updateOrCreate(['title' => $title], [
                'icon' => $icon, 'description' => $description, 'sort_order' => ($index + 1) * 10, 'is_active' => true,
            ]);
        }
    }
}
