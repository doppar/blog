<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyTwoFactorRequest;
use App\Http\Middleware\VerifyTwoFactorUser;
use App\Models\User;
use Phaseolies\Http\Response;
use Phaseolies\Support\Facades\Auth;
use Phaseolies\Utilities\Attributes\Mapper;
use Phaseolies\Utilities\Attributes\Route;

#[Mapper(prefix: 'verify/2fa', middleware: ['guest', VerifyTwoFactorUser::class])]
class TwoFactorAuthController extends Controller
{
    #[Route(uri: '/', name: 'verify.2fa')]
    public function index(): Response
    {
        return view('auth.2fa');
    }

    #[Route(uri: '/', methods: ['POST'], name: 'verify.2fa')]
    public function verifyToken(VerifyTwoFactorRequest $request): Response
    {
        $token = strtoupper(trim((string) $request->input('token')));
        $pendingUser = $this->pendingUser();

        if (!$pendingUser) {
            return redirect()->route('login')->withError('Your two-factor session has expired. Please sign in again.');
        }

        $verified = Auth::verifyTwoFactorCode($token) || Auth::verifyRecoveryCode($pendingUser, $token);

        if (!$verified) {
            return back()->withError('Invalid authentication code.');
        }

        if (!Auth::completeTwoFactorLogin()) {
            return back()->withError('Something went wrong while completing your login.');
        }

        $request->session()->forget('2fa_token');

        return redirect()->intended(route('admin.dashboard'))->withSuccess('Two-factor authentication verified.');
    }

    protected function pendingUser(): ?User
    {
        $actor = (string) config('auth.default', 'web');
        $userId = session('2fa_' . $actor . '_user_id');

        return $userId ? User::find($userId) : null;
    }
}
