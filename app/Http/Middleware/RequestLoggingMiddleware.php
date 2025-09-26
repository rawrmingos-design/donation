<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestLoggingMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        // Log suspicious requests
        $this->logSuspiciousActivity($request);
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);
        
        // Log request details
        $this->logRequest($request, $response, $duration);
        
        return $response;
    }

    /**
     * Log suspicious activity
     */
    protected function logSuspiciousActivity(Request $request): void
    {
        $suspiciousPatterns = [
            'sql_injection' => [
                '/(\bunion\b.*\bselect\b)|(\bselect\b.*\bunion\b)/i',
                '/\b(select|insert|update|delete|drop|create|alter)\b.*\b(from|into|table|database)\b/i',
                '/(\bor\b|\band\b).*[\'"].*[\'"].*(\bor\b|\band\b)/i',
            ],
            'xss_attempt' => [
                '/<script[^>]*>.*?<\/script>/i',
                '/javascript:/i',
                '/on\w+\s*=/i',
                '/<iframe[^>]*>.*?<\/iframe>/i',
            ],
            'path_traversal' => [
                '/\.\.\//',
                '/\.\.\\\\/',
                '/\.\.\%2f/i',
                '/\.\.\%5c/i',
            ],
            'command_injection' => [
                '/[;&|`$(){}]/i',
                '/\b(cat|ls|pwd|whoami|id|uname|wget|curl)\b/i',
            ]
        ];

        $requestData = json_encode([
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'input' => $request->except(['password', 'password_confirmation']),
            'headers' => $request->headers->all(),
        ]);

        foreach ($suspiciousPatterns as $type => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $requestData)) {
                    Log::warning("Suspicious {$type} detected", [
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'url' => $request->fullUrl(),
                        'method' => $request->method(),
                        'pattern' => $pattern,
                        'user_id' => $request->user()?->id,
                    ]);
                    break;
                }
            }
        }
    }

    /**
     * Log request details
     */
    protected function logRequest(Request $request, Response $response, float $duration): void
    {
        // Only log API requests and sensitive endpoints
        if (!$this->shouldLog($request)) {
            return;
        }

        $logData = [
            'ip' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status' => $response->getStatusCode(),
            'duration' => $duration,
            'user_id' => $request->user()?->id,
            'user_agent' => $request->userAgent(),
        ];

        // Log failed requests with more detail
        if ($response->getStatusCode() >= 400) {
            $logData['input'] = $request->except(['password', 'password_confirmation']);
            Log::warning('Failed request', $logData);
        } else {
            Log::info('Request processed', $logData);
        }
    }

    /**
     * Determine if request should be logged
     */
    protected function shouldLog(Request $request): bool
    {
        $logPaths = [
            '/api/',
            '/login',
            '/register',
            '/password/',
            '/donations/',
            '/campaigns/',
            '/withdrawals/',
        ];

        $path = $request->path();
        
        foreach ($logPaths as $logPath) {
            if (str_contains($path, $logPath)) {
                return true;
            }
        }

        return false;
    }
}
