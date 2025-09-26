<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SecureInput implements ValidationRule
{
    protected array $dangerousPatterns = [
        // SQL Injection patterns
        '/(\bunion\b.*\bselect\b)|(\bselect\b.*\bunion\b)/i',
        '/\b(select|insert|update|delete|drop|create|alter)\b.*\b(from|into|table|database)\b/i',
        '/(\bor\b|\band\b).*[\'"].*[\'"].*(\bor\b|\band\b)/i',
        
        // XSS patterns
        '/<script[^>]*>.*?<\/script>/i',
        '/javascript:/i',
        '/on\w+\s*=/i',
        '/<iframe[^>]*>.*?<\/iframe>/i',
        
        // Path traversal
        '/\.\.\//',
        '/\.\.\\\\/',
        '/\.\.\%2f/i',
        '/\.\.\%5c/i',
        
        // Command injection
        '/[;&|`$(){}]/i',
        '/\b(cat|ls|pwd|whoami|id|uname|wget|curl|rm|mv|cp)\b/i',
        
        // PHP code injection
        '/<\?php/i',
        '/<\?=/i',
        '/eval\s*\(/i',
        '/exec\s*\(/i',
        '/system\s*\(/i',
        '/shell_exec\s*\(/i',
    ];

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            return;
        }

        foreach ($this->dangerousPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                $fail("The {$attribute} contains potentially dangerous content.");
                return;
            }
        }

        // Check for excessive HTML tags (potential XSS)
        if (substr_count($value, '<') > 50) {
            $fail("The {$attribute} contains too many HTML tags.");
            return;
        }

        // Check for suspicious URL patterns
        if (preg_match('/https?:\/\/[^\s]+\.(tk|ml|ga|cf|bit\.ly|tinyurl|t\.co)/i', $value)) {
            $fail("The {$attribute} contains suspicious URLs.");
            return;
        }
    }
}
