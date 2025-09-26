<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictAdminFromFundraiser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // If user is not authenticated, let auth middleware handle it
        if (!$user) {
            return $next($request);
        }
        
        // If user is admin, redirect to Filament admin panel
        if ($user->role === 'admin') {
            // Log the attempt
            \Log::info('Admin user attempted to access creator route', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'requested_url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            return redirect('/admin')->with('info', 'Admin users should use the admin panel for creator management.');
        }
        
        return $next($request);
    }
}
