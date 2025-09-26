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
        $limiterKey = $this->resolveRequestSignature($request, $key);

        if (RateLimiter::tooManyAttempts($limiterKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($limiterKey);
            
            return response()->json([
                'error' => 'Too many requests. Please try again later.',
                'retry_after' => $seconds,
                'message' => "Rate limit exceeded. Try again in {$seconds} seconds."
            ], 429)->header('Retry-After', $seconds);
        }

        RateLimiter::hit($limiterKey, $decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', max(0, $maxAttempts - RateLimiter::attempts($limiterKey)));

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
}
