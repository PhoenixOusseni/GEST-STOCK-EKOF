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
        DepotActif::set($depot->id);

        return redirect()->route('dashboard')->with('success', 'Magazin ' . $depot->designation . ' sélectionné.');
    }

    public function quitterDepot()
    {
        DepotActif::forget();

        return redirect()->route('depots.accueil');
    }

    public function dashboard()
    {
        $depot = DepotActif::current();
        $depotId = $depot->id;

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

        // Ventes du mois
        $ventes_mois = $ventes_periodes['mois'];

        // Achats du mois
        $achats_mois = DepotActif::sommeAchats(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());

        // Encaissements du mois
        $encaissements_mois = DepotActif::encaissements()->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->sum('montant_encaisse');

        // Décaissements du mois
        $decaissements_mois = DepotActif::decaissements()->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->sum('montant_decaisse');

        // Dépenses du mois
        $depenses_mois = Depense::where('date_depense', '>=', Carbon::now()->startOfMonth())
            ->sum('montant');

        // Reste à solder des ventes et des achats
        $reste_a_solder_ventes = DepotActif::ventes()->sum('reste_a_payer');
        $reste_a_solder_achats = DepotActif::achats()->sum('reste_a_payer');
        $count_ventes_non_soldees = DepotActif::ventes()->where('reste_a_payer', '>', 0)->count();
        $count_achats_non_soldes = DepotActif::achats()->where('reste_a_payer', '>', 0)->count();

        // Top 5 articles vendus
        $top_articles_ventes = \DB::table('ventes_details')
            ->where('ventes_details.depot_id', $depotId)
            ->join('articles', 'ventes_details.article_id', '=', 'articles.id')
            ->select('articles.designation', \DB::raw('SUM(ventes_details.quantite) as total_quantite'),
                     \DB::raw('SUM(ventes_details.prix_total) as total_montant'))
            ->groupBy('articles.id', 'articles.designation')
            ->orderByDesc('total_quantite')
            ->limit(5)
            ->get();

        // Top 5 articles achetés - basé sur le montant des achats
        // Puisque achat_details n'a pas de structure complète, on utilise une approche alternative
        // On récupère juste les achats les plus importants
        $top_articles_achats = DepotActif::achats()->orderByDesc('montant_total')
            ->with(['fournisseur'])
            ->limit(5)
            ->get()
            ->map(function($achat) {
                return (object)[
                    'designation' => $achat->designation ?: 'Achat #' . $achat->numero_achat,
                    'total_quantite' => 1, // Nombre d'achats
                    'total_montant' => $achat->montant_total,
                ];
            });

        // Articles en stock faible
        $stock_faible = DepotActif::stocks()->with(['article', 'depot'])
            ->whereRaw('quantite_disponible < quantite_minimale')
            ->orderBy('quantite_disponible')
            ->limit(10)
            ->get();

        // Ventes récentes
        $ventes_recentes = DepotActif::ventes()->with(['client'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Achats récents
        $achats_recents = DepotActif::achats()->with(['fournisseur'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Dépenses récentes
        $depenses_recentes = Depense::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Ventes aujourd'hui
        $ventes_aujourd_hui = $ventes_periodes['jour_count'];

        // Achats aujourd'hui
        $achats_aujourd_hui = DepotActif::achats()->whereDate('date_achat', Carbon::today())->count();

        // Encaissements aujourd'hui
        $encaissements_aujourd_hui = DepotActif::encaissements()->where('created_at', '>=', Carbon::today())
            ->sum('montant_encaisse');

        // Préparer les statistiques totales
        $total_stats = [
            'ventes' => $stats['total_ventes'],
            'achats' => $stats['total_achats'],
            'clients' => $stats['total_clients'],
            'fournisseurs' => $stats['total_fournisseurs'],
            'articles' => $stats['total_articles'],
            'utilisateurs' => $stats['total_utilisateurs'],
            'encaissements' => DepotActif::encaissements()->count(),
            'decaissements' => DepotActif::decaissements()->count(),
            'depenses' => Depense::count(),
        ];

        // Statistiques d'aujourd'hui
        $today_stats = [
            'ventes_count' => $ventes_aujourd_hui,
            'achats_count' => $achats_aujourd_hui,
            'encaissements' => $encaissements_aujourd_hui,
        ];

        // Banques avec leurs soldes (encaissements, décaissements, reste à recouvrir)
        $banques_soldes = Banque::orderBy('designation')->get()->map(function ($banque) use ($depotId) {
            $encaissements = $banque->encaissements()->whereHas('vente.details', fn ($q) => $q->where('depot_id', $depotId));
            $decaissements = $banque->decaissements()->whereHas('achat.details', fn ($q) => $q->where('depot_id', $depotId));
            return [
                'id'              => $banque->id,
                'designation'     => $banque->designation,
                'total_encaisse'  => $encaissements->sum('montant_encaisse'),
                'total_decaisse'  => $decaissements->sum('montant_decaisse'),
                'solde_recouvrir' => $encaissements->sum('reste'),
            ];
        });

        // Caisses avec leurs soldes
        $caisses_soldes = Caisse::orderBy('designation')->get()->map(function ($caisse) use ($depotId) {
            $encaissements = $caisse->encaissements()->whereHas('vente.details', fn ($q) => $q->where('depot_id', $depotId));
            $decaissements = $caisse->decaissements()->whereHas('achat.details', fn ($q) => $q->where('depot_id', $depotId));
            return [
                'id'              => $caisse->id,
                'designation'     => $caisse->designation,
                'total_encaisse'  => $encaissements->sum('montant_encaisse'),
                'total_decaisse'  => $decaissements->sum('montant_decaisse'),
                'solde_recouvrir' => $encaissements->sum('reste'),
            ];
        });

        // Séries mensuelles (12 derniers mois) pour les graphiques
        $chart_labels = [];
        $chart_ventes = [];
        $chart_achats = [];
        $chart_encaissements = [];
        $chart_decaissements = [];
        for ($i = 11; $i >= 0; $i--) {
            $date  = Carbon::now()->startOfMonth()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end   = $date->copy()->endOfMonth();

            $chart_labels[]        = $date->translatedFormat('M Y');
            $chart_ventes[]        = DepotActif::sommeVentes($start, $end);
            $chart_achats[]        = DepotActif::sommeAchats($start, $end);
            $chart_encaissements[] = (float) DepotActif::encaissements()->whereBetween('created_at', [$start, $end])->sum('montant_encaisse');
            $chart_decaissements[] = (float) DepotActif::decaissements()->whereBetween('created_at', [$start, $end])->sum('montant_decaisse');
        }

        $chart_data = [
            'labels'        => $chart_labels,
            'ventes'        => $chart_ventes,
            'achats'        => $chart_achats,
            'encaissements' => $chart_encaissements,
            'decaissements' => $chart_decaissements,
        ];

        return view('pages.dashboard', compact(
            'depot',
            'ventes_periodes',
            'total_stats',
            'today_stats',
            'ventes_mois',
            'achats_mois',
            'encaissements_mois',
            'decaissements_mois',
            'depenses_mois',
            'reste_a_solder_ventes',
            'reste_a_solder_achats',
            'count_ventes_non_soldees',
            'count_achats_non_soldes',
            'top_articles_ventes',
            'top_articles_achats',
            'stock_faible',
            'ventes_recentes',
            'achats_recents',
            'depenses_recentes',
            'banques_soldes',
            'caisses_soldes',
            'chart_data'
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
