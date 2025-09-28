<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class IpBlockingMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        
        // Skip IP blocking for development environment
        if (config('app.env') === 'local') {
            return $next($request);
        }
        
        // Check if IP is permanently blocked
        if ($this->isPermanentlyBlocked($ip)) {
            Log::warning('Blocked IP attempted access', [
                'ip' => $ip,
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
            ]);
            
            return response()->json([
                'error' => 'Access denied',
                'message' => 'Your IP address has been blocked due to suspicious activity.'
            ], 403);
        }
        
        // Check if IP is temporarily blocked
        if ($this->isTemporarilyBlocked($ip)) {
            $remainingTime = Cache::get("temp_block:{$ip}");
            
            return response()->json([
                'error' => 'Temporarily blocked',
                'message' => 'Too many failed attempts. Please try again later.',
                'retry_after' => $remainingTime
            ], 429);
        }
        
        // Check whitelist
        if ($this->isWhitelisted($ip)) {
            return $next($request);
        }
        
        // Monitor suspicious activity (only for non-GET requests)
        if (!$request->isMethod('GET')) {
            $this->monitorSuspiciousActivity($request);
        }
        
        return $next($request);
    }
    
    /**
     * Check if IP is permanently blocked
     */
    protected function isPermanentlyBlocked(string $ip): bool
    {
        return Cache::has("perm_block:{$ip}");
    }
    
    /**
     * Check if IP is temporarily blocked
     */
    protected function isTemporarilyBlocked(string $ip): bool
    {
        return Cache::has("temp_block:{$ip}");
    }
    
    /**
     * Check if IP is whitelisted
     */
    protected function isWhitelisted(string $ip): bool
    {
        $whitelist = config('security.monitoring.ip_whitelist', []);
        return in_array($ip, $whitelist);
    }
    
    /**
     * Monitor suspicious activity
     */
    protected function monitorSuspiciousActivity(Request $request): void
    {
        $ip = $request->ip();
        
        // Only check for actually suspicious patterns, not every request
        if ($this->isSuspiciousRequest($request)) {
            $key = "suspicious_requests:{$ip}";
            $count = Cache::increment($key, 1);
            
            // Set expiry if this is the first increment
            if ($count === 1) {
                Cache::put($key, 1, now()->addHour());
            }
            
            Log::warning('Suspicious request detected', [
                'ip' => $ip,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
                'input' => $request->except(['password', 'password_confirmation']),
            ]);
            
            // Auto-block if threshold exceeded (increased threshold)
            $threshold = config('security.monitoring.failure_threshold', 25); // Increased from 10 to 25
            if ($count >= $threshold) {
                $this->temporarilyBlockIp($ip, 300); // 5 minutes
                
                Log::alert('IP automatically blocked due to suspicious activity', [
                    'ip' => $ip,
                    'suspicious_count' => $count,
                    'threshold' => $threshold,
                ]);
            }
        }
    }
    
    /**
     * Check if request is suspicious
     */
    protected function isSuspiciousRequest(Request $request): bool
    {
        // Skip checking for authenticated users (they're likely legitimate)
        if ($request->user()) {
            return false;
        }
        
        // Skip checking for safe routes
        if ($this->isSafeRoute($request)) {
            return false;
        }
        
        $suspiciousPatterns = [
            // SQL injection attempts (more specific patterns)
            '/(\bunion\b.*\bselect\b)|(\bselect\b.*\bunion\b)/i',
            '/\b(select|insert|update|delete|drop)\b.*\b(from|into|table)\b/i',
            '/\'\s*(or|and)\s*\'/i',
            
            // XSS attempts (more specific)
            '/<script[^>]*>.*<\/script>/i',
            '/javascript:\s*[^;]/i',
            '/on(load|click|error|focus)\s*=/i',
            
            // Path traversal (more specific)
            '/\.\.\/.*\.\.\//',
            '/\.\.\\\\.*\.\.\\\\/',
            
            // Command injection (exclude common characters)
            '/[;&|`]\s*(rm|cat|ls|wget|curl|nc|bash)/i',
        ];
        
        // Only check input data, not URLs (URLs can contain legitimate special chars)
        $inputData = json_encode($request->except(['_token', '_method']));
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $inputData)) {
                return true;
            }
        }
        
        // Check for rapid requests (increased threshold for development)
        $threshold = config('app.env') === 'local' ? 500 : 200; // Much higher threshold
        $requestCount = Cache::get("request_count:{$request->ip()}", 0);
        if ($requestCount > $threshold) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if route is safe and shouldn't be monitored aggressively
     */
    protected function isSafeRoute(Request $request): bool
    {
        $safeRoutes = [
            '/',
            '/campaigns',
            '/campaigns/*',
            '/about',
            '/contact',
            '/faq',
            '/terms-of-service',
            '/privacy-policy',
            '/how-it-works',
            '/dashboard',
        ];
        
        $currentPath = $request->path();
        
        foreach ($safeRoutes as $route) {
            if ($route === $currentPath || (str_ends_with($route, '*') && str_starts_with($currentPath, rtrim($route, '*')))) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Temporarily block an IP
     */
    public function temporarilyBlockIp(string $ip, int $seconds = 3600): void
    {
        Cache::put("temp_block:{$ip}", $seconds, now()->addSeconds($seconds));
    }
    
    /**
     * Permanently block an IP
     */
    public function permanentlyBlockIp(string $ip): void
    {
        Cache::forever("perm_block:{$ip}", true);
    }
}
