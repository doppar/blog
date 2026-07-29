<?php

namespace App\Http\Middleware;

use App\Models\User;
use Phaseolies\Support\Facades\Auth;
use Phaseolies\Middleware\Contracts\Middleware;
use Phaseolies\Http\Response;
use Phaseolies\Http\Request;
use Closure;
use Phaseolies\Http\Exceptions\HttpResponseException;

class AdminMiddleware implements Middleware
{
    /**
     * Handle an incoming request
     *
     * @param Request $request
     * @param \Closure(\Phaseolies\Http\Request) $next
     * @return Phaseolies\Http\Response
     */
    public function __invoke(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            if ($request->isAjax() || $request->wantsJson()) {
                throw new HttpResponseException('Unauthenticated', 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->role !== User::ROLE_ADMIN) {
            if ($request->isAjax() || $request->wantsJson()) {
                throw new HttpResponseException('Forbidden - Admin access required', 403);
            }
            $request->session()->flash('error', 'Admin access required');
            return redirect()->route('home');
        }

        return $next($request);
    }
}
