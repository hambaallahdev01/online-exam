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
        $remoteIp = (string) $request->server('REMOTE_ADDR', '');
        $trustedProxies = (array) config('services.cloudflare.trusted_proxies', []);
        $isTrustedProxy = collect($trustedProxies)->contains(
            fn (string $proxy) => $this->ipMatches($remoteIp, $proxy)
        );

        if ($isTrustedProxy && $request->hasHeader('CF-Connecting-IP')) {
            $cfIp = $request->header('CF-Connecting-IP');
            if (filter_var($cfIp, FILTER_VALIDATE_IP)) {
                $request->server->set('REMOTE_ADDR', $cfIp);
            }
        }

        return $next($request);
    }

    private function ipMatches(string $ip, string $network): bool
    {
        if ($ip === '' || $network === '') {
            return false;
        }

        if (! str_contains($network, '/')) {
            return hash_equals($network, $ip);
        }

        [$subnet, $prefix] = explode('/', $network, 2);
        $ipBinary = @inet_pton($ip);
        $subnetBinary = @inet_pton($subnet);

        if ($ipBinary === false || $subnetBinary === false || strlen($ipBinary) !== strlen($subnetBinary)) {
            return false;
        }

        $prefixLength = filter_var($prefix, FILTER_VALIDATE_INT);
        $maxBits = strlen($ipBinary) * 8;
        if ($prefixLength === false || $prefixLength < 0 || $prefixLength > $maxBits) {
            return false;
        }

        $fullBytes = intdiv($prefixLength, 8);
        if ($fullBytes > 0 && ! hash_equals(substr($subnetBinary, 0, $fullBytes), substr($ipBinary, 0, $fullBytes))) {
            return false;
        }

        $remainingBits = $prefixLength % 8;
        if ($remainingBits === 0) {
            return true;
        }

        $mask = (0xFF << (8 - $remainingBits)) & 0xFF;

        return (ord($ipBinary[$fullBytes]) & $mask) === (ord($subnetBinary[$fullBytes]) & $mask);
    }
}
