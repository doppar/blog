<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Phaseolies\Http\Response;
use Phaseolies\Support\Facades\Auth;
use Phaseolies\Utilities\Attributes\Mapper;
use Phaseolies\Utilities\Attributes\Route;

#[Mapper(prefix: 'login')]
class LoginController extends Controller
{
    #[Route(uri: '/', name: 'login', middleware: ['guest'])]
    public function index(): Response
    {
        if (User::count() === 0) {
            return redirect()->route('register');
        }

        return view('auth.login', [
            'allowRegistration' => false,
        ]);
    }

    #[Route(uri: '/', methods: ['POST'], name: 'login', middleware: ['guest'])]
    public function login(LoginRequest $request): Response
    {
        $credentials = $request->passed();
        $remember = filter_var($request->input('remember', false), FILTER_VALIDATE_BOOLEAN);
        $email = strtolower(trim((string) ($credentials['email'] ?? '')));
        $user = User::where('email', $email)->first();

        if (!$user || !Auth::try($credentials, $remember)) {
            return back()->withError('Email or password is incorrect.');
        }

        if (Auth::hasTwoFactorEnabled($user)) {
            $rawToken = bin2hex(random_bytes(64));
            $timestamp = time();
            $signature = hash_hmac('sha256', $user->id . '|' . $rawToken . '|' . $timestamp, config('app.key'));

            session()->put('2fa_token', implode('|', [$user->id, $rawToken, $signature, $timestamp]));

            return redirect()->route('verify.2fa');
        }

        return redirect()->intended(route('admin.dashboard'))->withSuccess('You are logged in.');
    }

    #[Route(uri: '/logout', methods: ['POST'], name: 'logout', middleware: ['auth'])]
    public function logout(): Response
    {
        Auth::logout();

        return redirect()->route('login')->withSuccess('You are successfully logged out.');
    }
}
