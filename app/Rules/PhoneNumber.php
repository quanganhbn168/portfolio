<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Số điện thoại: số quốc tế dạng +<mã quốc gia><số> (8–15 chữ số, theo E.164) hoặc số Việt Nam
 * (di động 03/05/07/08/09, cố định 02x, tổng đài 1800/1900; chấp nhận +84/84).
 * Cho phép khoảng trắng, dấu chấm, gạch ngang và ngoặc khi nhập.
 */
final class PhoneNumber implements ValidationRule
{
    /** Giữ chữ số; số quốc tế (không phải +84) giữ dấu +, số Việt Nam đổi +84/84 về 0 (0901234567). */
    public static function normalize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with(ltrim($value), '+') && ! str_starts_with($digits, '84')) {
            return '+'.$digits;
        }

        // 84901234567 -> 0901234567 ; 840901234567 (nhập thừa số 0) -> 0901234567
        if (str_starts_with($digits, '84') && strlen($digits) >= 11) {
            $digits = '0'.ltrim(substr($digits, 2), '0');
        }

        return $digits;
    }

    public static function isValid(string $normalized): bool
    {
        if (str_starts_with($normalized, '+')) {
            return (bool) preg_match('/^\+[1-9]\d{7,14}$/', $normalized);
        }

        return (bool) preg_match('/^(?:0[35789]\d{8}|02\d{8,9}|1[89]00\d{4,6})$/', $normalized);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! self::isValid((string) self::normalize($value))) {
            $fail(__('Số điện thoại không hợp lệ. Vui lòng nhập số điện thoại hợp lệ, ví dụ +60 12 345 6789.'));
        }
    }
}
