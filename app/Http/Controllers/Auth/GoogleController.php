<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the Google callback.
     */
    public function handleGoogleCallback(Request $request)
    {
        // try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists with Google ID
            $user = User::where('google_id', $googleUser->getId())->first();
            
            if ($user) {
                // User exists, log them in
                Auth::login($user);
                return redirect()->intended('/');
            }
            
            // Check if user exists with same email
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // Link Google account to existing user
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'google_token_expires_at' => now()->addSeconds($googleUser->expiresIn ?? 3600),
                ]);
                Auth::login($user);
                return redirect()->intended('/');
            }
            
            // Split Google name into first and last name
            $googleName = $googleUser->getName();
            $nameParts = explode(' ', $googleName, 2);
            $firstName = $nameParts[0] ?? $googleName;
            $lastName = $nameParts[1] ?? '';

            // Create new user
            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'google_token' => $googleUser->token,
                'google_token_expires_at' => now()->addSeconds($googleUser->expiresIn ?? 3600),
                'email_verified_at' => now(),
            ]);
            
            Auth::login($user);
            return redirect()->intended('/');
            
        // } catch (\Exception $e) {
        //     return redirect()->route('login')->with('error', 'Unable to login with Google. Please try again.');
        // }
    }
}