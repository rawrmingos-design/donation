<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnhancedCsrfMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip CSRF for safe methods
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            return $next($request);
        }

        // Skip CSRF for whitelisted routes
        if ($this->shouldSkipCsrf($request)) {
            return $next($request);
        }

        // Enhanced CSRF validation
        if (!$this->validateCsrfToken($request)) {
            Log::warning('CSRF token validation failed', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->header('referer'),
            ]);

            // Track CSRF failures
            $this->trackCsrfFailure($request);

            return response()->json([
                'error' => 'CSRF token validation failed',
                'message' => 'Your session has expired. Please refresh the page and try again.'
            ], 419);
        }

        return $next($request);
    }

    /**
     * Check if CSRF should be skipped for this request
     */
    protected function shouldSkipCsrf(Request $request): bool
    {
        $skipRoutes = config('security.csrf_protection.exclude_routes', []);
        
        foreach ($skipRoutes as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate CSRF token with enhanced checks
     */
    protected function validateCsrfToken(Request $request): bool
    {
        $token = $request->input('_token') 
                ?? $request->header('X-CSRF-TOKEN') 
                ?? $request->header('X-XSRF-TOKEN');

        if (!$token) {
            return false;
        }

        // Basic token validation
        if (!hash_equals(session()->token(), $token)) {
            return false;
        }

        // Check token age (prevent replay attacks)
        $tokenAge = $this->getTokenAge($request);
        $maxAge = config('security.csrf_protection.token_lifetime', 3600);
        
        if ($tokenAge > $maxAge) {
            return false;
        }

        // Check referer header for additional security
        if (!$this->validateReferer($request)) {
            return false;
        }

        // Check for double-submit cookie pattern
        if (!$this->validateDoubleSubmitCookie($request)) {
            return false;
        }

        return true;
    }

    /**
     * Get token age in seconds
     */
    protected function getTokenAge(Request $request): int
    {
        $tokenCreated = session()->get('_token_created_at', now()->timestamp);
        return now()->timestamp - $tokenCreated;
    }

    /**
     * Validate referer header
     */
    protected function validateReferer(Request $request): bool
    {
        $referer = $request->header('referer');
        
        if (!$referer) {
            return false;
        }

        $allowedHosts = [
            $request->getHost(),
            parse_url(config('app.url'), PHP_URL_HOST),
        ];

        $refererHost = parse_url($referer, PHP_URL_HOST);
        
        return in_array($refererHost, $allowedHosts);
    }

    /**
     * Validate double-submit cookie
     */
    protected function validateDoubleSubmitCookie(Request $request): bool
    {
        $cookieToken = $request->cookie('XSRF-TOKEN');
        $headerToken = $request->header('X-XSRF-TOKEN');
        
        if (!$cookieToken || !$headerToken) {
            return true; // Skip if not using double-submit pattern
        }

        return hash_equals($cookieToken, $headerToken);
    }

    /**
     * Track CSRF failure for monitoring
     */
    protected function trackCsrfFailure(Request $request): void
    {
        $ip = $request->ip();
        $key = "csrf_failures:{$ip}";
        
        $failures = Cache::increment($key, 1);
        
        // Set expiry if this is the first failure
        if ($failures === 1) {
            Cache::put($key, 1, now()->addHour());
        }

        // Block IP if too many CSRF failures
        if ($failures >= 10) {
            Cache::put("temp_block:{$ip}", 3600, now()->addHour());
            
            Log::alert('IP blocked due to excessive CSRF failures', [
                'ip' => $ip,
                'failures' => $failures,
            ]);
        }
    }
}
