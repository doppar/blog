<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Phaseolies\Http\Response;
use Phaseolies\Support\Facades\Auth;
use Phaseolies\Utilities\Attributes\Mapper;
use Phaseolies\Utilities\Attributes\Route;

#[Mapper(prefix: 'register', middleware: ['guest'])]
class RegisterController extends Controller
{
    #[Route(uri: '/', name: 'register')]
    public function index(): Response
    {
        if (User::count() > 0) {
            return redirect()->route('login');
        }

        return view('auth.register');
    }

    #[Route(uri: '/', methods: ['POST'], name: 'register')]
    public function register(RegisterRequest $request): Response
    {
        if (User::count() > 0) {
            return redirect()->route('login');
        }

        $user = User::create([
            'name' => trim((string) $request->input('name')),
            'email' => strtolower(trim((string) $request->input('email'))),
            'password' => (string) $request->input('password'),
            'role' => User::ROLE_ADMIN,
            'status' => true,
        ]);

        Auth::login($user);

        return redirect()->route('admin.profile.index')->withSuccess('Your administrator account has been created.');
    }
}
