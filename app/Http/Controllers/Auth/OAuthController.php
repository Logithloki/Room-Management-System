<?php
// app/Http/Controllers/Auth/OAuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    public function redirectToMicrosoft()
    {
        return Socialite::driver('microsoft')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function handleMicrosoftCallback()
    {
        try {
            $microsoftUser = Socialite::driver('microsoft')->user();
            
            // Check if user exists by Microsoft ID
            $existingUser = User::where('microsoft_id', $microsoftUser->getId())->first();
            
            if ($existingUser) {
                // Update existing user's Microsoft token
                $existingUser->update([
                    'microsoft_token' => [
                        'access_token' => $microsoftUser->token,
                        'refresh_token' => $microsoftUser->refreshToken,
                        'expires_in' => $microsoftUser->expiresIn,
                    ],
                    'last_login_at' => now(),
                ]);
                
                Auth::login($existingUser);
                
                return redirect()->intended(route('dashboard'));
            }
            
            // Check if user exists by email
            $existingEmailUser = User::where('email', $microsoftUser->getEmail())->first();
            
            if ($existingEmailUser) {
                // Link Microsoft account to existing user
                $existingEmailUser->update([
                    'microsoft_id' => $microsoftUser->getId(),
                    'microsoft_token' => [
                        'access_token' => $microsoftUser->token,
                        'refresh_token' => $microsoftUser->refreshToken,
                        'expires_in' => $microsoftUser->expiresIn,
                    ],
                    'email_verified_at' => now(), // Auto-verify email for SSO
                    'last_login_at' => now(),
                ]);
                
                Auth::login($existingEmailUser);
                
                return redirect()->intended(route('dashboard'));
            }
            
            // Create new user
            $user = User::create([
                'name' => $microsoftUser->getName(),
                'email' => $microsoftUser->getEmail(),
                'microsoft_id' => $microsoftUser->getId(),
                'microsoft_token' => [
                    'access_token' => $microsoftUser->token,
                    'refresh_token' => $microsoftUser->refreshToken,
                    'expires_in' => $microsoftUser->expiresIn,
                ],
                'email_verified_at' => now(), // Auto-verify email for SSO
                'password' => Hash::make(Str::random(32)), // Random password for security
                'role' => 'lecturer', // Default role
                'last_login_at' => now(),
            ]);
            
            Auth::login($user);
            
            return redirect()->intended(route('dashboard'));
            
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Unable to authenticate with Microsoft. Please try again.',
            ]);
        }
    }
}