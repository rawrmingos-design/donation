<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains security-related configuration options for the
    | donation platform application.
    |
    */

    'rate_limits' => [
        'api' => [
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],
        'auth' => [
            'max_attempts' => 5,
            'decay_minutes' => 15,
        ],
        'donation' => [
            'max_attempts' => 5,
            'decay_minutes' => 1,
        ],
        'campaign' => [
            'max_attempts' => 3,
            'decay_minutes' => 10,
        ],
        'upload' => [
            'max_attempts' => 10,
            'decay_minutes' => 5,
        ],
        'share' => [
            'max_attempts' => 30,
            'decay_minutes' => 1,
        ],
    ],

    'file_upload' => [
        'max_size' => 10 * 1024 * 1024, // 10MB
        'allowed_mime_types' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
        'allowed_extensions' => [
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx'
        ],
        'scan_for_malware' => env('SCAN_UPLOADS_FOR_MALWARE', true),
        'quarantine_suspicious' => env('QUARANTINE_SUSPICIOUS_FILES', true),
    ],

    'input_validation' => [
        'max_input_length' => 10000,
        'max_array_depth' => 5,
        'sanitize_html' => true,
        'strip_dangerous_tags' => true,
        'allowed_html_tags' => '<p><br><strong><em><u><ol><ul><li><h1><h2><h3><h4><h5><h6><blockquote><a><img>',
    ],

    'password_policy' => [
        'min_length' => 8,
        'max_length' => 128,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_special_chars' => true,
        'max_consecutive_chars' => 3,
        'check_common_passwords' => true,
        'check_sequential_chars' => true,
        'check_keyboard_patterns' => true,
    ],

    'session_security' => [
        'regenerate_on_login' => true,
        'invalidate_on_logout' => true,
        'timeout_minutes' => 120,
        'secure_cookies' => env('SESSION_SECURE_COOKIE', true),
        'same_site' => 'strict',
    ],

    'csrf_protection' => [
        'enabled' => true,
        'token_lifetime' => 3600, // 1 hour
        'exclude_routes' => [
            'webhooks/*',
            'api/public/*',
        ],
    ],

    'content_security_policy' => [
        'enabled' => true,
        'report_only' => env('CSP_REPORT_ONLY', false),
        'report_uri' => env('CSP_REPORT_URI', null),
        'directives' => [
            'default-src' => "'self'",
            'script-src' => "'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com https://js.stripe.com",
            'style-src' => "'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
            'font-src' => "'self' https://fonts.gstatic.com https://cdn.jsdelivr.net",
            'img-src' => "'self' data: https: blob:",
            'connect-src' => "'self' https://api.stripe.com",
            'frame-src' => "'self' https://js.stripe.com",
            'object-src' => "'none'",
            'base-uri' => "'self'",
            'form-action' => "'self'",
            'frame-ancestors' => "'none'",
            'upgrade-insecure-requests' => true,
        ],
    ],

    'security_headers' => [
        'x_content_type_options' => 'nosniff',
        'x_frame_options' => 'DENY',
        'x_xss_protection' => '1; mode=block',
        'referrer_policy' => 'strict-origin-when-cross-origin',
        'permissions_policy' => 'geolocation=(), microphone=(), camera=()',
        'hsts_max_age' => 31536000, // 1 year
        'hsts_include_subdomains' => true,
        'hsts_preload' => true,
    ],

    'logging' => [
        'log_suspicious_requests' => true,
        'log_failed_logins' => true,
        'log_file_uploads' => true,
        'log_admin_actions' => true,
        'retention_days' => 90,
    ],

    'monitoring' => [
        'alert_on_multiple_failures' => true,
        'failure_threshold' => 10,
        'alert_email' => env('SECURITY_ALERT_EMAIL', 'admin@example.com'),
        'block_suspicious_ips' => env('BLOCK_SUSPICIOUS_IPS', false),
        'ip_whitelist' => explode(',', env('IP_WHITELIST', '')),
    ],
];
