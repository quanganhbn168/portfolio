<?php

namespace App\Support\Branding;

/** Đọc bảng màu trong config/brand.php và in thành biến CSS cho website. */
final class BrandColors
{
    /** @return array<string, string> tên => #rrggbb, chỉ gồm các giá trị hợp lệ */
    public static function all(): array
    {
        return collect((array) config('brand.colors', []))
            ->filter(fn (mixed $hex, mixed $name): bool => is_string($name)
                && preg_match('/^[a-z][a-z0-9-]*$/', $name) === 1
                && is_string($hex)
                && preg_match('/^#[0-9a-fA-F]{6}$/', $hex) === 1)
            ->map(fn (string $hex): string => strtolower($hex))
            ->all();
    }

    public static function hex(string $name, string $fallback = '#000000'): string
    {
        return self::all()[$name] ?? $fallback;
    }

    /** `:root{--brand-primary:#0e3283;--brand-primary-rgb:14, 50, 131;...}` */
    public static function css(): string
    {
        $declarations = '';

        foreach (self::all() as $name => $hex) {
            [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');
            $declarations .= "--brand-{$name}:{$hex};--brand-{$name}-rgb:{$r}, {$g}, {$b};";
        }

        return ':root{'.$declarations.'}';
    }
}
