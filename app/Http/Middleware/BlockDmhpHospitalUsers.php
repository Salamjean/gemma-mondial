<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Déconnecte les utilisateurs déjà connectés (compte hôpital et personnel)
 * dont l'hôpital a été marqué comme appartenant au DMHP.
 */
class BlockDmhpHospitalUsers
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->isBlockedByDmhpHospital()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = 'Accès non autorisé : votre établissement appartient au DMHP.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 403);
            }

            return redirect('login')->withErrors($message);
        }

        return $next($request);
    }
}
