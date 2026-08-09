<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestoreCloudflareRealIp
{
    /**
     * Handle an incoming request and restore true client IP from Cloudflare header.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasHeader('CF-Connecting-IP')) {
            $cfIp = $request->header('CF-Connecting-IP');
            if (filter_var($cfIp, FILTER_VALIDATE_IP)) {
                $request->server->set('REMOTE_ADDR', $cfIp);
            }
        }

        return $next($request);
    }
}
