# Portfolio

Website portfolio giới thiệu các dự án đã triển khai (website, landing page, phần mềm). Laravel 13 + Filament 5 (admin) + Bootstrap 5.3 (frontend), tông màu nâu cam.

## Trang công khai

| Đường dẫn | Nội dung |
|-----------|----------|
| `/` | Portfolio: hero + số liệu, tab lọc theo lĩnh vực (kèm số lượng), lưới dự án, case study, CTA |
| `/blog` | Blog cá nhân (danh sách, phân trang); bài viết ở `/{slug}` |
| `/{slug}` | Chi tiết dự án, danh mục dự án hoặc bài viết blog |
| `/about` | Giới thiệu |
| `/contact` | Form liên hệ (lưu vào admin, gửi email nếu có cấu hình) |

`/projects` chuyển hướng về `/`. Số liệu ở hero lấy từ database; case study là dự án nổi bật (hoặc mới nhất).

## Cài đặt local (Laragon, PHP 8.3, MySQL)

```bash
composer install
pnpm install
cp .env.example .env && php artisan key:generate
# tạo database `portfolio` rồi:
php artisan migrate --seed      # seed dữ liệu mẫu (5 lĩnh vực, 6 dự án)
php artisan app:setup-admin     # sinh quyền Shield + tạo admin@example.com / admin123 (super_admin)
php artisan storage:link
pnpm dev                        # hoặc pnpm build
```

Admin: `/admin`. Chỉ role `super_admin` vào được panel. Đổi email, mật khẩu tại `/admin/profile`; `app:setup-admin` nhận `--email=` và `--password=`, chạy lại nhiều lần vẫn an toàn.

Quản trị: **Dự án** (kèm danh mục, ảnh, thư viện, nội dung chi tiết), **Trang**, **Yêu cầu tư vấn**, **Bình luận**, **FAQ**, **Menu**, **Thư viện media**, **Cài đặt website** (logo, liên hệ, SEO, trang Giới thiệu).

## Đổi màu / giao diện

- Bảng màu duy nhất nằm ở `config/brand.php` (nâu cam), được in thành biến CSS `--brand-*`. Đổi màu chỉ cần sửa file này.
- CSS thuần trong `resources/css/frontend/`: `theme.css` (nối biến vào Bootstrap), `components/*.css` (dùng chung), `pages/*.css` (riêng từng trang; trang portfolio là `pages/projects-index.css`). Sau khi sửa: `pnpm build`.

## Ngôn ngữ (EN / VI)

Mặc định tiếng Anh, công tắc ở header chuyển sang Tiếng Việt (cookie `lang`). Chữ cố định trong giao diện viết bằng `__('Câu tiếng Việt')`, bản tiếng Anh ở `lang/en.json` (test `LocaleTest` báo nếu thiếu). Nội dung từ database nhập một thứ tiếng; khi chọn Tiếng Việt, Google Translate dịch phần nội dung đó.

## Ảnh

Ảnh tải lên giữ nguyên bản gốc; khi hiển thị, Curator + Glide tạo bản WebP theo chiều rộng (`/curator/<file>?w=768&fm=webp`). Trong view dùng `<x-responsive-image>` hoặc các accessor `image_webp_url`, `image_card_url`, `image_thumb_url`.

## Lệnh hay dùng

```bash
php artisan test            # cần database portfolio_testing đã migrate: DB_DATABASE=portfolio_testing php artisan migrate
vendor/bin/pint             # format code
composer audit              # kiểm tra lỗ hổng dependency
```

## Triển khai production

Dùng `.env.production.example` làm mẫu `.env`. Các bước: `composer install --no-dev -o`, `pnpm install && pnpm build`, `php artisan key:generate`, `php artisan curator:token`, `php artisan migrate --force`, `php artisan db:seed --force` (lần đầu), `php artisan app:setup-admin --email=... --password=...`, `php artisan storage:link`, `php artisan optimize`. Ngoài ra: `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, đổi `CACHE_STORE`/`SESSION_DRIVER`/`QUEUE_CONNECTION` sang database hoặc redis, cấu hình trusted proxies nếu chạy sau Cloudflare/proxy. Thêm resource admin mới thì chạy lại `app:setup-admin` để sinh quyền.
