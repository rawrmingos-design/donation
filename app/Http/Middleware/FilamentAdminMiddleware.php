<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FilamentAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // If user is not authenticated, redirect to login
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access admin panel.');
        }
        
        // If user role is not admin or creator, deny access
        if (!in_array($user->role, ['admin', 'creator'])) {
            // Log the unauthorized access attempt
            \Log::warning('Unauthorized Filament access attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'user_email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'requested_url' => $request->fullUrl()
            ]);
            
            // Clear the session to force re-authentication
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')->with('error', 'Access denied. Admin privileges required.');
        }
        
        return $next($request);
    }
}
