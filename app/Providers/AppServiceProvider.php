<?php

namespace App\Providers;

use App\Models\Achat;
use App\Models\Article;
use App\Models\Banque;
use App\Models\BonCommande;
use App\Models\Caisse;
use App\Models\Client;
use App\Models\Decaissement;
use App\Models\Depense;
use App\Models\Depot;
use App\Models\Encaissement;
use App\Models\Entree;
use App\Models\Fournisseur;
use App\Models\Inventaire;
use App\Models\Sortie;
use App\Models\Transfert;
use App\Models\User;
use App\Models\Vente;
use App\Observers\TraceActivite;
use App\Support\DepotActif;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('depotActif', DepotActif::current());
        });

        foreach ([
            Vente::class,
            BonCommande::class,
            Entree::class,
            Sortie::class,
            Inventaire::class,
            Achat::class,
            Depense::class,
            Encaissement::class,
            Decaissement::class,
            Transfert::class,
            Article::class,
            Client::class,
            Fournisseur::class,
            User::class,
            Depot::class,
            Banque::class,
            Caisse::class,
        ] as $model) {
            $model::observe(TraceActivite::class);
        }
    }
}
