<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        \Log::info("Redirecting to Google");
        return Socialite::driver('google')->with(['verify' => false])->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
                ->user();

            if (!Str::endsWith($googleUser->getEmail(), '@gmail.com')) {
                return redirect()->route('login')->withErrors(['email' => 'Only Gmail accounts are allowed.']);
            }

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                Auth::login($user, true);
            } else {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => bcrypt(Str::random(16)),
                ]);

                Auth::login($user, true);
            }

            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            \Log::error('Google Authentication Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['error' => 'Failed to authenticate with Google.']);
        }
    }
}
