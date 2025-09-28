<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Different CSP policies for different environments
        if (config('app.env') === 'local') {
            $this->setDevelopmentCSP($response);
        } else {
            $this->setProductionCSP($response);
        }

        // Security Headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        // HSTS (HTTP Strict Transport Security)
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Remove server information
        $response->headers->remove('Server');
        $response->headers->remove('X-Powered-By');

        return $response;
    }

    /**
     * Set development CSP - very permissive for local development
     */
    private function setDevelopmentCSP(Response $response): void
    {
        // Very permissive CSP for development - allows all sources
        $csp = [
            "default-src *",
            "script-src * 'unsafe-inline' 'unsafe-eval'",
            "style-src * 'unsafe-inline'",
            "font-src * data:",
            "img-src * data: blob:",
            "connect-src *",
            "frame-src *",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action *"
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $csp));
    }

    /**
     * Set production CSP - strict security policy
     */
    private function setProductionCSP(Response $response): void
    {
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com https://js.stripe.com https://checkout.stripe.com https://api.tokopay.id https://checkout.tokopay.id",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net https://cdn.jsdelivr.net https://checkout.tokopay.id",
            "font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net https://cdn.jsdelivr.net",
            "img-src 'self' data: https: blob: https://checkout.tokopay.id https://api.tokopay.id",
            "connect-src 'self' https://api.stripe.com https://checkout.stripe.com https://api.tokopay.id https://checkout.tokopay.id https://fonts.bunny.net",
            "frame-src 'self' https://js.stripe.com https://checkout.stripe.com https://checkout.tokopay.id",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self' https://checkout.tokopay.id",
            "frame-ancestors 'none'",
            "upgrade-insecure-requests"
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $csp));
    }
}
