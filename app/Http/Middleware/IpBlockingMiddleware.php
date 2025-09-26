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
        
        // Monitor suspicious activity
        $this->monitorSuspiciousActivity($request);
        
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
        $key = "suspicious_activity:{$ip}";
        
        // Increment suspicious activity counter
        $count = Cache::increment($key, 1);
        
        // Set expiry if this is the first increment
        if ($count === 1) {
            Cache::put($key, 1, now()->addHour());
        }
        
        // Check for suspicious patterns
        if ($this->isSuspiciousRequest($request)) {
            Cache::increment("suspicious_requests:{$ip}", 1);
            Cache::put("suspicious_requests:{$ip}", Cache::get("suspicious_requests:{$ip}", 0), now()->addHour());
            
            Log::warning('Suspicious request detected', [
                'ip' => $ip,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
                'input' => $request->except(['password', 'password_confirmation']),
            ]);
        }
        
        // Auto-block if threshold exceeded
        $suspiciousCount = Cache::get("suspicious_requests:{$ip}", 0);
        if ($suspiciousCount >= config('security.monitoring.failure_threshold', 10)) {
            $this->temporarilyBlockIp($ip, 3600); // 1 hour
            
            Log::alert('IP automatically blocked due to suspicious activity', [
                'ip' => $ip,
                'suspicious_count' => $suspiciousCount,
            ]);
        }
    }
    
    /**
     * Check if request is suspicious
     */
    protected function isSuspiciousRequest(Request $request): bool
    {
        $suspiciousPatterns = [
            // SQL injection attempts
            '/(\bunion\b.*\bselect\b)|(\bselect\b.*\bunion\b)/i',
            '/\b(select|insert|update|delete|drop)\b.*\b(from|into|table)\b/i',
            
            // XSS attempts
            '/<script[^>]*>/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            
            // Path traversal
            '/\.\.\//',
            '/\.\.\\\\/',
            
            // Command injection
            '/[;&|`$(){}]/i',
        ];
        
        $requestData = json_encode([
            'url' => $request->fullUrl(),
            'input' => $request->all(),
        ]);
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $requestData)) {
                return true;
            }
        }
        
        // Check for rapid requests (more than 100 requests per minute)
        $requestCount = Cache::get("request_count:{$request->ip()}", 0);
        if ($requestCount > 100) {
            return true;
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
