<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Ngôn ngữ website: lấy từ cookie "lang" (en | vi); chưa chọn thì dùng ngôn ngữ mặc định (en). */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys((array) config('languages.supported', []));
        $locale = (string) $request->cookie(config('languages.cookie', 'lang'), '');

        if (! in_array($locale, $supported, true)) {
            $locale = (string) config('app.locale', 'en');
        }

        app()->setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
