<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleBasedAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $allowedRoles  Comma-separated list of allowed roles
     */
    public function handle(Request $request, Closure $next, string $allowedRoles): Response
    {
        $user = Auth::user();
        
        // If user is not authenticated, let auth middleware handle it
        if (!$user) {
            return $next($request);
        }
        
        $allowedRolesArray = explode(':', $allowedRoles);

        // Check if user's role is in the allowed roles
        if (!in_array($user->role, $allowedRolesArray)) {
            // Log the unauthorized access attempt
            \Log::warning('Unauthorized role-based access attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'user_email' => $user->email,
                'allowed_roles' => $allowedRolesArray,
                'requested_url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // Redirect based on user role
            return $this->redirectBasedOnRole($user->role);
        }
        
        return $next($request);
    }
    
    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole(string $role): Response
    {
        switch ($role) {
            case 'admin':
                return redirect('/admin')->with('info', 'Admin users should use the admin panel.');
                
            case 'creator':
                return redirect('/dashboard')->with('info', 'Access restricted. Please use appropriate features for your role.');
                
            case 'donor':
                return redirect('/dashboard')->with('info', 'Access restricted. Please explore campaigns or make donations.');
                
            default:
                return redirect('/')->with('error', 'Access denied.');
        }
    }
}
