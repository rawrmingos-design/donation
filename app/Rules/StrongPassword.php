<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    protected array $commonPasswords = [
        'password', '123456', '123456789', 'qwerty', 'abc123', 'password123',
        'admin', 'letmein', 'welcome', 'monkey', '1234567890', 'password1',
        'qwerty123', 'admin123', 'root', 'toor', 'pass', 'test', 'guest',
        'user', 'demo', 'sample', 'temp', 'default', 'changeme'
    ];

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail("The {$attribute} must be a string.");
            return;
        }

        // Minimum length check
        if (strlen($value) < 8) {
            $fail("The {$attribute} must be at least 8 characters long.");
            return;
        }

        // Maximum length check (prevent DoS)
        if (strlen($value) > 128) {
            $fail("The {$attribute} must not exceed 128 characters.");
            return;
        }

        // Check for at least one uppercase letter
        if (!preg_match('/[A-Z]/', $value)) {
            $fail("The {$attribute} must contain at least one uppercase letter.");
            return;
        }

        // Check for at least one lowercase letter
        if (!preg_match('/[a-z]/', $value)) {
            $fail("The {$attribute} must contain at least one lowercase letter.");
            return;
        }

        // Check for at least one number
        if (!preg_match('/[0-9]/', $value)) {
            $fail("The {$attribute} must contain at least one number.");
            return;
        }

        // Check for at least one special character
        if (!preg_match('/[^A-Za-z0-9]/', $value)) {
            $fail("The {$attribute} must contain at least one special character.");
            return;
        }

        // Check against common passwords
        if (in_array(strtolower($value), $this->commonPasswords)) {
            $fail("The {$attribute} is too common. Please choose a more secure password.");
            return;
        }

        // Check for repeated characters (more than 3 consecutive)
        if (preg_match('/(.)\1{3,}/', $value)) {
            $fail("The {$attribute} must not contain more than 3 consecutive identical characters.");
            return;
        }

        // Check for sequential characters
        if ($this->hasSequentialChars($value)) {
            $fail("The {$attribute} must not contain sequential characters like '123' or 'abc'.");
            return;
        }

        // Check for keyboard patterns
        if ($this->hasKeyboardPattern($value)) {
            $fail("The {$attribute} must not contain keyboard patterns like 'qwerty' or 'asdf'.");
            return;
        }
    }

    /**
     * Check for sequential characters
     */
    protected function hasSequentialChars(string $password): bool
    {
        $sequences = [
            '0123456789',
            'abcdefghijklmnopqrstuvwxyz',
            'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
        ];

        foreach ($sequences as $sequence) {
            for ($i = 0; $i <= strlen($sequence) - 4; $i++) {
                $substr = substr($sequence, $i, 4);
                if (str_contains(strtolower($password), strtolower($substr))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check for keyboard patterns
     */
    protected function hasKeyboardPattern(string $password): bool
    {
        $patterns = [
            'qwerty', 'qwertz', 'azerty', 'asdf', 'zxcv',
            '1234', '4321', 'abcd', 'dcba'
        ];

        $lowerPassword = strtolower($password);

        foreach ($patterns as $pattern) {
            if (str_contains($lowerPassword, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
