<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Redirect;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if ($request->route()->getName() === 'login') {
            return $next($request);
        }
        if (!$request->user() ||
            ($request->user() instanceof MustVerifyEmail &&
                !$request->user()->hasVerifiedEmail())) {
            auth()->logout();
            return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : Redirect::route('login')->with('notVerified', __('Your email address is not verified. Please verify your email to access this page.'));
        }

        return $next($request);
    }
}
