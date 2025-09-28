<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $key = 'global', int $maxAttempts = 60, int $decayMinutes = 1): ResponseAlias
    {
        // Skip rate limiting for safe navigation methods in development
        if (config('app.env') === 'local' && $request->isMethod('GET')) {
            return $next($request);
        }

        $limiterKey = $this->resolveRequestSignature($request, $key);

        // Adjust limits based on request type and environment
        [$adjustedAttempts, $adjustedDecay] = $this->adjustLimitsForRequest($request, $key, $maxAttempts, $decayMinutes);

        if (RateLimiter::tooManyAttempts($limiterKey, $adjustedAttempts)) {
            $seconds = RateLimiter::availableIn($limiterKey);
            
            return response()->json([
                'error' => 'Too many requests. Please try again later.',
                'retry_after' => $seconds,
                'message' => "Rate limit exceeded. Try again in {$seconds} seconds."
            ], 429)->header('Retry-After', $seconds);
        }

        RateLimiter::hit($limiterKey, $adjustedDecay * 60);

        $response = $next($request);

        // Add rate limit headers
        $response->headers->set('X-RateLimit-Limit', $adjustedAttempts);
        $response->headers->set('X-RateLimit-Remaining', max(0, $adjustedAttempts - RateLimiter::attempts($limiterKey)));

        return $response;
    }

    /**
     * Resolve the rate limiter key for the request.
     */
    protected function resolveRequestSignature(Request $request, string $key): string
    {
        $user = $request->user();
        $ip = $request->ip();
        
        return match ($key) {
            'api' => "api:{$ip}",
            'auth' => "auth:{$ip}",
            'donation' => "donation:" . ($user ? $user->id : $ip),
            'campaign' => "campaign:" . ($user ? $user->id : $ip),
            'upload' => "upload:" . ($user ? $user->id : $ip),
            'share' => "share:{$ip}",
            default => "global:{$ip}",
        };
    }

    /**
     * Adjust rate limits based on request type and environment
     */
    protected function adjustLimitsForRequest(Request $request, string $key, int $maxAttempts, int $decayMinutes): array
    {
        $isLocal = config('app.env') === 'local';
        $isAuthenticated = $request->user() !== null;

        // More generous limits for development
        if ($isLocal) {
            $maxAttempts *= 3; // Triple the limits in development
            $decayMinutes = max(1, $decayMinutes / 2); // Faster decay
        }

        // More generous limits for authenticated users
        if ($isAuthenticated) {
            $maxAttempts = (int) ($maxAttempts * 1.5); // 50% more for authenticated users
        }

        // Adjust based on request type
        return match ($key) {
            'global' => [$maxAttempts * 2, $decayMinutes], // Very generous for general browsing
            'api' => [$maxAttempts, $decayMinutes],
            'auth' => [max(10, $maxAttempts), max(5, $decayMinutes)], // More restrictive for auth
            'donation' => [max(10, $maxAttempts), max(2, $decayMinutes)], // Moderate for donations
            'campaign' => [$maxAttempts, $decayMinutes * 2], // Longer decay for campaign operations
            'upload' => [max(20, $maxAttempts), $decayMinutes], // Reasonable for uploads
            'share' => [$maxAttempts * 3, $decayMinutes], // Very generous for sharing
            default => [$maxAttempts, $decayMinutes],
        };
    }
}
