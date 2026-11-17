<?php

namespace App\Http\Middleware;

use App\Support\Acces;
use App\Support\DepotActif;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureDepotActif
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->role === Acces::MAGAZIN) {
            if (! $user->depot_id) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'telephone' => 'Aucun magazin n\'est affecté à ce compte.',
                ]);
            }
            DepotActif::set((int) $user->depot_id);
        }

        $depot = DepotActif::current();
        if (!$depot) {
            return redirect()->route('depots.accueil');
        }

        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'], true) && $request->exists('depot_id')) {
            $request->merge(['depot_id' => $depot->id]);
        }

        return $next($request);
    }
}
