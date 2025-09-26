<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialAuthController extends Controller
{
    /**
     * Blocked email addresses that cannot use OAuth login
     */
    private const BLOCKED_EMAILS = [
        'superadmin@platform.com'
    ];

    /**
     * Redirect to Google OAuth provider
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            return $this->handleSocialLogin($googleUser, 'google');
        } catch (Exception $e) {
            return redirect('/login')->withErrors([
                'oauth' => 'Terjadi kesalahan saat login dengan Google. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Redirect to Facebook OAuth provider
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Handle Facebook OAuth callback
     */
    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
            return $this->handleSocialLogin($facebookUser, 'facebook');
        } catch (Exception $e) {
            return redirect('/login')->withErrors([
                'oauth' => 'Terjadi kesalahan saat login dengan Facebook. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Handle social login logic for both Google and Facebook
     */
    private function handleSocialLogin($socialUser, $provider)
    {
        // Check if email is blocked
        if (in_array($socialUser->getEmail(), self::BLOCKED_EMAILS)) {
            return redirect('/login')->withErrors([
                'oauth' => 'Akses login dengan email ini telah diblokir. Silakan gunakan email lain atau login manual.'
            ]);
        }

        // Check if user already exists
        $existingUser = User::where('email', $socialUser->getEmail())->first();


        if ($existingUser) {
            // Update user's OAuth information if needed
            $this->updateUserOAuthInfo($existingUser, $socialUser, $provider);
            
            // Login the existing user
            Auth::login($existingUser, true);
            
            return redirect()->intended('/dashboard');
        }

        // Create new user
        $user = $this->createUserFromSocial($socialUser, $provider);
        
        // Login the new user
        Auth::login($user, true);
        
        return redirect('/dashboard');
    }

    /**
     * Create a new user from social login data
     */
    private function createUserFromSocial($socialUser, $provider)
    {
        return User::create([
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make(uniqid()), // Random password since they'll use OAuth
            'avatar' => $socialUser->getAvatar(),
            'role' => 'donor', // Default role for OAuth users
            'is_verified' => true,
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
        ]);
    }

    /**
     * Update existing user's OAuth information
     */
    private function updateUserOAuthInfo($user, $socialUser, $provider)
    {
        $updateData = [];

        // Update avatar if not set or if it's from the same provider
        if (!$user->avatar || $user->provider === $provider) {
            $updateData['avatar'] = $socialUser->getAvatar();
        }

        // Update provider info if not set
        if (!$user->provider) {
            $updateData['provider'] = $provider;
            $updateData['provider_id'] = $socialUser->getId();
        }

        // Mark email as verified
        if (!$user->email_verified_at) {
            $updateData['email_verified_at'] = now();
            $updateData['is_verified'] = true;
        }

        if (!empty($updateData)) {
            $user->update($updateData);
        }
    }
}
