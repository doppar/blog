<?php

namespace App\Http\Controllers;

use Phaseolies\Utilities\Attributes\Route;
use Doppar\OAuthic\OAuthic;
use App\Models\User;
use Phaseolies\Http\Request;
use Phaseolies\Support\Facades\Auth;

class OAuthController extends Controller
{
    #[Route(uri: 'auth/google/redirect', name: 'auth.google.redirect')]
    public function redirect(Request $request)
    {
        // Remember where the visitor came from (e.g. a post page) so the
        // callback below can send them back there via redirect()->intended().
        $redirectTo = (string) $request->input('redirect_to', '');

        if ($this->isSafeLocalRedirect($redirectTo)) {
            $request->session()->put('url.intended', $redirectTo);
        }

        return redirect(OAuthic::driver('google')->redirect());
    }

    /**
     * Only allow same-site, root-relative paths as a post-login redirect
     * target, to avoid an open-redirect via a crafted "redirect_to" value.
     *
     * @param string $url
     * @return bool
     */
    private function isSafeLocalRedirect(string $url): bool
    {
        return $url !== '' && str_starts_with($url, '/') && !str_starts_with($url, '//');
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

        return redirect()->intended('/');
    }
}
