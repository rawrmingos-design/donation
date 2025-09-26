<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ResourceRoleAccess
{
    /**
     * Handle an incoming request for resource-specific role access.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $requiredResourceAndRole  The required role for this resource
     */
    public function handle(Request $request, Closure $next,  string $requiredResourceAndRole): Response
    {
        $user = Auth::user();
        
        // If user is not authenticated, let auth middleware handle it
        if (!$user) {
            return $next($request);
        }

        $arrayExplodeRequiredResourceAndRole = explode(':', $requiredResourceAndRole);
        
        // Check if user's role matches the required role for this resource
        if ($user->role !== $arrayExplodeRequiredResourceAndRole[1]) {
            // Log the unauthorized resource access attempt
            \Log::warning('Unauthorized resource access attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'user_email' => $user->email,
                'resource' => $arrayExplodeRequiredResourceAndRole[0],
                'required_role' => $arrayExplodeRequiredResourceAndRole[1],
                'requested_url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
            ]);
            
            // Return 403 Forbidden with custom message
            abort(403, "Access denied. This {$arrayExplodeRequiredResourceAndRole[0]} resource requires '{$arrayExplodeRequiredResourceAndRole[1]}' role. Your current role is '{$user->role}'.");
        }
        
        return $next($request);
    }
}
