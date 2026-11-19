<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LanguageController extends Controller
{
    /** Chọn ngôn ngữ: lưu cookie "lang" và đặt/xoá cookie của Google Translate để dịch nội dung từ database. */
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(array_key_exists($locale, (array) config('languages.supported', [])), 404);

        $default = (string) config('languages.default', 'en');
        $minutes = 60 * 24 * 365;

        Cookie::queue(config('languages.cookie', 'lang'), $locale, $minutes);

        if ($locale === $default) {
            Cookie::queue(Cookie::forget('googtrans'));
        } else {
            Cookie::queue(cookie('googtrans', '/'.$default.'/'.$locale, $minutes, '/', null, null, false));
        }

        $back = $request->input('return_to');
        $path = is_string($back) && str_starts_with($back, '/') && ! str_starts_with($back, '//') ? $back : route('home');

        return redirect()->to($path);
    }
}
