<?php

namespace App\Providers;

use App\Models\Critere;
use App\Models\Diligence;
use App\Models\Service;
use App\Models\Traitement;
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
    }
}
