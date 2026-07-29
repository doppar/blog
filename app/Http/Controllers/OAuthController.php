<?php

namespace App\Http\Controllers;

use Phaseolies\Utilities\Attributes\Route;
use Doppar\OAuthic\OAuthic;
use App\Models\User;
use Phaseolies\Support\Facades\Auth;

class OAuthController extends Controller
{
    #[Route(uri: 'auth/google/redirect', name: 'auth.google.redirect')]
    public function redirect()
    {
        return redirect(OAuthic::driver('google')->redirect());
    }

    #[Route(uri: 'auth/google/callback', name: 'auth.google.callback')]
    public function callback()
    {
        $provider = OAuthic::driver('google')->user();

        $email = $provider->getEmail();

        if (!$email) {
            return redirect()->route('login')->withError('Google did not share an email address with us.');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $provider->getName() ?? explode('@', $email)[0],
                'email' => $email,
                'role' => 'author',
                'image' => $provider->getAvatar(),
                'password' => bcrypt(str()->random(32)),
            ]);
        } elseif (empty($user->image) && $provider->getAvatar()) {
            $user->update([
                'image' => $provider->getAvatar(),
            ]);
        }

        Auth::login($user);

        return redirect()->route('home');
    }
}
