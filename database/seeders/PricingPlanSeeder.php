<?php

namespace Database\Seeders;

use App\Models\PricingPlan;
use Illuminate\Database\Seeder;

/** Bảng giá mẫu (số tiền chỉ là ví dụ): sửa trong admin → Dịch vụ & bảng giá → Bảng giá. */
final class PricingPlanSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['Landing Page', '3.000.000đ', '/ trang', 'Trang đích cho chiến dịch quảng cáo hoặc giới thiệu một sản phẩm.', ['1 trang cuộn, thiết kế riêng', 'Responsive mọi thiết bị', 'Form liên hệ gửi email', 'Chuẩn SEO cơ bản', 'Bảo hành 1 tháng'], false, 'Nhận báo giá'],
            ['Website Doanh nghiệp', '8.000.000đ', '/ website', 'Website giới thiệu công ty đầy đủ, tự quản trị nội dung.', ['Tới 8 trang nội dung', 'Trang quản trị dễ dùng', 'Blog / tin tức', 'Chuẩn SEO, tốc độ cao', 'Hosting & SSL năm đầu', 'Bảo hành 6 tháng'], true, 'Nhận báo giá'],
            ['Phần mềm theo yêu cầu', 'Liên hệ', null, 'Hệ thống quản lý riêng cho quy trình của bạn.', ['Khảo sát & phân tích nghiệp vụ', 'Thiết kế cơ sở dữ liệu', 'Phân quyền người dùng', 'Báo cáo & xuất Excel', 'Đào tạo & hỗ trợ vận hành'], false, 'Trao đổi yêu cầu'],
        ] as $index => [$name, $price, $note, $description, $features, $featured, $button]) {
            PricingPlan::query()->updateOrCreate(['name' => $name], [
                'price' => $price, 'price_note' => $note, 'description' => $description, 'features' => $features,
                'is_featured' => $featured, 'button_label' => $button, 'sort_order' => ($index + 1) * 10, 'is_active' => true,
            ]);
        }
    }
}
