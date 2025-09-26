<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInputMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        
        $sanitized = $this->sanitizeArray($input);
        
        $request->merge($sanitized);

        return $next($request);
    }

    /**
     * Recursively sanitize array data
     */
    protected function sanitizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                $data[$key] = $this->sanitizeString($value, $key);
            }
        }

        return $data;
    }

    /**
     * Sanitize string input based on field type
     */
    protected function sanitizeString(string $value, string $key): string
    {
        // Don't sanitize password fields
        if (in_array($key, ['password', 'password_confirmation', 'current_password'])) {
            return $value;
        }

        // Don't sanitize rich text content fields
        if (in_array($key, ['description', 'content', 'short_desc'])) {
            return $this->sanitizeRichText($value);
        }

        // Basic sanitization for other fields
        $value = trim($value);
        $value = strip_tags($value);
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

        return $value;
    }

    /**
     * Sanitize rich text content (allow safe HTML tags)
     */
    protected function sanitizeRichText(string $value): string
    {
        $allowedTags = '<p><br><strong><em><u><ol><ul><li><h1><h2><h3><h4><h5><h6><blockquote><a><img>';
        
        // Strip dangerous tags but keep allowed ones
        $value = strip_tags($value, $allowedTags);
        
        // Remove dangerous attributes
        $value = preg_replace('/(<[^>]+)\s+(on\w+|javascript:|vbscript:|data:)[^>]*>/i', '$1>', $value);
        
        // Clean up href attributes in links
        $value = preg_replace_callback('/<a\s+[^>]*href\s*=\s*["\']([^"\']*)["\'][^>]*>/i', function ($matches) {
            $href = $matches[1];
            // Only allow http, https, and mailto links
            if (!preg_match('/^(https?:\/\/|mailto:)/i', $href)) {
                return str_replace($matches[1], '#', $matches[0]);
            }
            return $matches[0];
        }, $value);

        return $value;
    }
}
