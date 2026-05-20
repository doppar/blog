<?php

namespace App\Http\Controllers;

use Phaseolies\Support\Facades\Auth;
use Phaseolies\Utilities\Attributes\Route;
use Phaseolies\Http\Response;

class WelcomeController extends Controller
{
    /**
     * Show the welcome page
     *
     * @return Response
     */
    #[Route(uri: '/', name: 'home')]
    public function welcome(): Response
    {
        return Auth::check()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('login');
    }
}
