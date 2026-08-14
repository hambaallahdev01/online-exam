<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetTenantLocale
{
    /**
     * Supported application locales.
     */
    protected array $supportedLocales = ['id', 'en', 'ar', 'zh'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->school_id && $user->school) {
            $schoolLocale = $user->school->locale ?? 'id';

            if (in_array($schoolLocale, $this->supportedLocales)) {
                App::setLocale($schoolLocale);
            } else {
                App::setLocale('id');
            }
        }

        return $next($request);
    }
}
