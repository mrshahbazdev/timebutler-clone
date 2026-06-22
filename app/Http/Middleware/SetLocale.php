<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', auth()->user()?->locale ?? config('app.locale'));

        if (in_array($locale, ['en', 'de'])) {
            App::setLocale($locale);
        }

        if ($user = auth()->user()) {
            $timezone = $user->organization?->timezone;
            if ($timezone && in_array($timezone, timezone_identifiers_list())) {
                config(['app.timezone' => $timezone]);
                date_default_timezone_set($timezone);
            }
        }

        return $next($request);
    }
}
