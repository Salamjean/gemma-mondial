<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotMinistereMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            if (in_array(Auth::user()->role_as, ['ministere', 'super'])) {
                if (Auth::user()->role_as == 'ministere') {
                    $ministere = Auth::user()->ministere;
                    if ($ministere && ($ministere->status == 1 || $ministere->delete == 1)) {
                        Auth::logout();
                        return redirect('/login')->withErrors('Votre compte Ministère est suspendu ou désactivé.');
                    }
                }
                return $next($request);
            } else {
                return redirect('/dashboard');
            }
        } else {
            return redirect('/login');
        }
    }
}
