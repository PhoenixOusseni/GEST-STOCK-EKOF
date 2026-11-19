<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vente;
use App\Models\Achat;
use App\Models\Encaissement;
use App\Models\Decaissement;
use App\Models\Client;
use App\Models\Fournisseur;
use App\Models\Depense;
use App\Models\Article;
use App\Models\Depot;
use App\Models\Stock;
use App\Models\User;
use App\Models\Banque;
use App\Models\Caisse;
use App\Models\VenteDetail;
use App\Support\Acces;
use App\Support\DepotActif;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function auth()
    {
        return view('login-admin');
    }

    public function accueilDepots()
    {
        if (Acces::estMagazin() && auth()->user()->depot_id) {
            DepotActif::set((int) auth()->user()->depot_id);

            return redirect()->route('dashboard');
        }

        $depots = Depot::query()
            ->withCount(['stocks as articles_en_stock' => fn ($q) => $q->where('quantite_disponible', '>', 0)])
            ->withSum('stocks as quantite_totale', 'quantite_disponible')
            ->orderBy('designation')
            ->get();

        $ventesMois = VenteDetail::query()
            ->select('depot_id', DB::raw('SUM(prix_total) as total'))
            ->whereHas('vente', fn ($q) => $q->where('date_vente', '>=', now()->startOfMonth()->toDateString()))
            ->groupBy('depot_id')
            ->pluck('total', 'depot_id');

        return view('pages.depots.accueil', compact('depots', 'ventesMois'));
    }

    public function choisirDepot(Depot $depot)
    {
        if (Acces::estMagazin() && (int) $depot->id !== (int) auth()->user()->depot_id) {
            abort(403);
        }

        DepotActif::set($depot->id);

        return redirect()->route('dashboard')->with('success', 'Magazin ' . $depot->designation . ' sélectionné.');
    }

    public function quitterDepot()
    {
        if (Acces::estMagazin()) {
            return redirect()->route('dashboard');
        }

        DepotActif::forget();

        return redirect()->route('depots.accueil');
    }

    public function dashboard()
    {
        $depot = DepotActif::current();

        $ventes_periodes = [
            'jour' => DepotActif::sommeVentes(now()->startOfDay(), now()->endOfDay()),
            'jour_count' => DepotActif::ventes()->whereDate('date_vente', now()->toDateString())->count(),
            'semaine' => DepotActif::sommeVentes(now()->startOfWeek(), now()->endOfWeek()),
            'semaine_count' => DepotActif::ventes()->whereBetween('date_vente', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])->count(),
            'mois' => DepotActif::sommeVentes(now()->startOfMonth(), now()->endOfMonth()),
            'mois_count' => DepotActif::ventes()->whereBetween('date_vente', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])->count(),
            'annee' => DepotActif::sommeVentes(now()->startOfYear(), now()->endOfYear()),
            'annee_count' => DepotActif::ventes()->whereBetween('date_vente', [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()])->count(),
        ];

        // Statistiques générales
        $stats = [
            'total_ventes' => DepotActif::ventes()->count(),
            'total_achats' => DepotActif::achats()->count(),
            'total_clients' => Client::count(),
            'total_fournisseurs' => Fournisseur::count(),
            'total_articles' => DepotActif::stocks()->distinct('article_id')->count('article_id'),
            'total_utilisateurs' => User::count(),
        ];

        $ventes_mois = $ventes_periodes['mois'];
        $achats_mois = DepotActif::sommeAchats(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());
        $depenses_mois = Depense::where('date_depense', '>=', Carbon::now()->startOfMonth())->sum('montant');

        $ventesNonSoldees = DepotActif::ventes()->where('reste_a_payer', '>', 0);
        $reste_a_solder_ventes = (clone $ventesNonSoldees)->sum('reste_a_payer');
        $count_ventes_non_soldees = (clone $ventesNonSoldees)->count();
        $reste_a_solder_achats = DepotActif::achats()->where('reste_a_payer', '>', 0)->sum('reste_a_payer');
        $count_achats_non_soldes = DepotActif::achats()->where('reste_a_payer', '>', 0)->count();

        $ventes_non_soldees = (clone $ventesNonSoldees)
            ->with('client')
            ->orderBy('date_vente')
            ->orderBy('id')
            ->get();

        $total_stats = [
            'ventes' => $stats['total_ventes'],
            'achats' => $stats['total_achats'],
            'clients' => $stats['total_clients'],
            'fournisseurs' => $stats['total_fournisseurs'],
            'articles' => $stats['total_articles'],
            'utilisateurs' => $stats['total_utilisateurs'],
            'depenses' => Depense::count(),
        ];

        return view('pages.dashboard', compact(
            'depot',
            'ventes_periodes',
            'total_stats',
            'ventes_mois',
            'achats_mois',
            'depenses_mois',
            'reste_a_solder_ventes',
            'reste_a_solder_achats',
            'count_ventes_non_soldees',
            'count_achats_non_soldes',
            'ventes_non_soldees'
        ));
    }

    public function etat()
    {
        return view('pages.etats.index');
    }

    public function parametres()
    {
        return view('pages.parametres.index');
    }

    /**
     * Effacer le cache de l'application
     */
    public function clearCache()
    {
        \Artisan::call('cache:clear');
        return redirect()->back()->with('success', 'Le cache a été vidé avec succès.');
    }

    /**
     * Effacer les logs
     */
    public function clearLogs()
    {
        try {
            $logFiles = glob(storage_path('logs/*.log'));
            foreach ($logFiles as $logFile) {
                unlink($logFile);
            }
            return redirect()->back()->with('success', 'Les logs ont été réinitialisés avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la réinitialisation des logs.');
        }
    }

    /**
     * Exécuter les migrations
     */
    public function migrate()
    {
        try {
            \Artisan::call('migrate', ['--force' => true]);
            return redirect()->back()->with('success', 'Les migrations ont été exécutées avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'exécution des migrations.');
        }
    }

    /**
     * help method
     */
    public function help()
    {
        return view('pages.help');
    }

    // Article with code-barre generation logic moved to Article model
    public function codeBarArticle()
    {
        $articles = Article::all();
        return view('pages.articles.view_codeBar_article', compact('articles'));
    }

    // Print code-barres for a specific article
    public function printCodeBarArticle()
    {
        $articles = Article::all();
        return view('pages.articles.print_codeBar_article', compact('articles'));
    }
}
