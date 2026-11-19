<?php

namespace App\Http\Middleware;

use App\Support\Acces;
use App\Support\DepotActif;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestreindreRole
{
    private const PROFIL = [
        'logout',
        'utilisateurs.updateProfil',
        'utilisateurs.updatePassword',
    ];

    private const ADMIN_SEUL = [
        'parametres',
        'parametres.update',
        'cache.clear',
        'logs.clear',
        'logs.index',
        'migrate',
        'stocks.reset',
        'gestions_depots.create',
        'gestions_depots.store',
        'gestions_depots.edit',
        'gestions_depots.update',
        'gestions_depots.destroy',
        'depots.import',
    ];

    private const CAISSE_BLOQUE = [
        'gestions_bons_commandes.edit',
        'gestions_bons_commandes.update',
        'gestions_bons_commandes.destroy',
        'bons_commandes.transformer',
        'gestions_ventes.edit',
        'gestions_ventes.update',
        'gestions_ventes.destroy',
        'gestions_entrees.edit',
        'gestions_entrees.update',
        'gestions_entrees.destroy',
        'gestions_sorties.edit',
        'gestions_sorties.update',
        'gestions_sorties.destroy',
        'gestions_inventaires.edit',
        'gestions_inventaires.update',
        'gestions_inventaires.destroy',
        'gestions_inventaires.valider',
        'gestions_inventaires.annuler',
        'gestions_inventaires.removeArticle',
        'gestions_depenses.edit',
        'gestions_depenses.update',
        'gestions_depenses.destroy',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        if ($user->role === Acces::MAGAZIN && $user->depot_id) {
            DepotActif::set((int) $user->depot_id);
        }

        $route = $request->route()?->getName() ?? '';

        if ($this->reserveAdmin($route)) {
            return $this->refus($request, 'Seul l\'administrateur peut gérer cette section.');
        }

        if (Acces::estCaisse() && in_array($route, self::CAISSE_BLOQUE, true)) {
            return $this->refus($request, 'La caisse ne peut pas modifier ni supprimer ce document.');
        }

        if (! Acces::estAdmin() && ! Acces::estCaisse() && $this->interditEnConsultation($request, $route)) {
            return $this->refus($request, 'Consultation uniquement : vous ne pouvez pas ajouter, modifier ni supprimer.');
        }

        return $next($request);
    }

    private function reserveAdmin(string $route): bool
    {
        if (Acces::estAdmin()) {
            return false;
        }

        return str_starts_with($route, 'gestions_utilisateurs.')
            || str_starts_with($route, 'entetes.')
            || in_array($route, self::ADMIN_SEUL, true);
    }

    private function interditEnConsultation(Request $request, string $route): bool
    {
        if (in_array($route, self::PROFIL, true)) {
            return false;
        }

        if (! in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return true;
        }

        return str_ends_with($route, '.create') || str_ends_with($route, '.edit');
    }

    private function refus(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            abort(403, $message);
        }

        if (url()->previous() === $request->fullUrl()) {
            return redirect()->route(Acces::estMagazin() ? 'dashboard' : 'depots.accueil')->with('error', $message);
        }

        return redirect()->back()->with('error', $message);
    }
}
