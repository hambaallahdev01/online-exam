<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Stevebauman\Location\Facades\Location;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromIp
{
    /**
     * Supported application locales.
     */
    protected array $supportedLocales = ['id', 'en', 'ar', 'zh'];

    /**
     * Arab League country codes.
     */
    protected array $arabicCountries = [
        'SA', 'AE', 'QA', 'KW', 'BH', 'OM', 'EG', 'JO', 'LB', 'IQ',
        'YE', 'SY', 'SD', 'DZ', 'MA', 'TN', 'LY', 'MR', 'SO', 'DJ', 'KM', 'PS'
    ];

    /**
     * Chinese speaking country/territory codes.
     */
    protected array $chineseCountries = [
        'CN', 'TW', 'HK', 'MO'
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is already authenticated with a school, SetTenantLocale will handle it
        if ($request->user() && $request->user()->school_id) {
            return $next($request);
        }

        $sessionKey = 'visitor_geo_locale';

        if ($request->has('lang') && in_array($request->query('lang'), $this->supportedLocales)) {
            $locale = $request->query('lang');
            Session::put($sessionKey, $locale);
            App::setLocale($locale);
            return $next($request);
        }

        if (Session::has($sessionKey)) {
            $locale = Session::get($sessionKey);
            if (in_array($locale, $this->supportedLocales)) {
                App::setLocale($locale);
                return $next($request);
            }
        }

        $ip = $request->ip();
        $locale = 'id'; // Primary default locale

        // Check if IP is not localhost / private subnet
        $resolvedFromIp = false;
        if ($ip && !in_array($ip, ['127.0.0.1', '::1']) && !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            try {
                $position = class_exists(Location::class) ? Location::get($ip) : null;
                if ($position && !empty($position->countryCode)) {
                    $country = strtoupper($position->countryCode);

                    if ($country === 'ID') {
                        $locale = 'id';
                    } elseif (in_array($country, $this->arabicCountries)) {
                        $locale = 'ar';
                    } elseif (in_array($country, $this->chineseCountries)) {
                        $locale = 'zh';
                    } else {
                        // English (GB/International) for all other countries
                        $locale = 'en';
                    }
                    $resolvedFromIp = true;
                }
            } catch (\Throwable $e) {
                // Fail gracefully and use default Indonesian locale
                Log::warning('GeoIP locale resolution failed: ' . $e->getMessage());
                $locale = 'id';
            }
        }

        // If IP could not be resolved (e.g. search engine bots or local network), check HTTP Accept-Language
        if (!$resolvedFromIp && $request->server('HTTP_ACCEPT_LANGUAGE')) {
            $preferred = $request->getPreferredLanguage($this->supportedLocales);
            if ($preferred && in_array($preferred, $this->supportedLocales)) {
                $locale = $preferred;
            }
        }

        Session::put($sessionKey, $locale);
        App::setLocale($locale);

        return $next($request);
    }
}
