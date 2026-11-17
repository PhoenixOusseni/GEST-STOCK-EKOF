<?php

namespace App\Http\Middleware;

use App\Support\DepotActif;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDepotActif
{
    public function handle(Request $request, Closure $next): Response
    {
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
