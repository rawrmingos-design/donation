<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\FilamentAdminMiddleware;
use App\Http\Middleware\RateLimitMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\SanitizeInputMiddleware;
use App\Http\Middleware\RequestLoggingMiddleware;
use App\Http\Middleware\SecureFileUploadMiddleware;
use App\Http\Middleware\IpBlockingMiddleware;
use App\Http\Middleware\EnhancedCsrfMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);
        $middleware->validateCsrfTokens(except: [
            'webhooks/midtrans',
            'webhooks/tokopay'
        ]);
        $middleware->web(append: [
            IpBlockingMiddleware::class,
            SecurityHeadersMiddleware::class,
            SanitizeInputMiddleware::class,
            RequestLoggingMiddleware::class,
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
        
        // Register security and admin middleware aliases
        $middleware->alias([
            'filament.admin' => FilamentAdminMiddleware::class,
            'restrict.admin.dashboard' => \App\Http\Middleware\RestrictAdminFromDashboard::class,
            'restrict.admin.fundraiser' => \App\Http\Middleware\RestrictAdminFromFundraiser::class,
            'role' => \App\Http\Middleware\RoleBasedAccess::class,
            'resource.role' => \App\Http\Middleware\ResourceRoleAccess::class,
            'rate.limit' => RateLimitMiddleware::class,
            'secure.upload' => SecureFileUploadMiddleware::class,
            'ip.blocking' => IpBlockingMiddleware::class,
            'enhanced.csrf' => EnhancedCsrfMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
