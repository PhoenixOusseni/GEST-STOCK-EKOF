<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EnteteController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DepotController;
use App\Http\Controllers\EntreeController;
use App\Http\Controllers\SortieController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\RapportStockController;
use App\Http\Controllers\TransfertController;
use App\Http\Controllers\BanqueController;
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\BonCommandeController;
use App\Http\Controllers\EncaissementController;
use App\Http\Controllers\AchatController;
use App\Http\Controllers\DecaissementController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\UserController;


Route::get('/', [PageController::class, 'auth'])->name('login');
Route::post('connexion', [AuthController::class, 'login_admin'])->name('login_admin');
Route::post('deconnexion', [AuthController::class, 'logout'])->name('logout');

// Routes protégées
Route::middleware(['auth', 'role.acces'])->group(function () {
    Route::get('accueil', [PageController::class, 'accueilDepots'])->name('depots.accueil');
    Route::post('accueil/depot/{depot}', [PageController::class, 'choisirDepot'])->name('depots.choisir');
    Route::get('accueil/quitter', [PageController::class, 'quitterDepot'])->name('depots.quitter');

    Route::resource('depots/gestions_depots', DepotController::class);
    Route::post('depots/import', [DepotController::class, 'import'])->name('depots.import');
    Route::get('depots/template', [DepotController::class, 'template'])->name('depots.template');

    Route::middleware('depot.actif')->group(function () {
    Route::get('dashboard', [PageController::class, 'dashboard'])->name('dashboard');

    // Gestion des entêtes
    Route::get('entetes/infos_societe', [EnteteController::class, 'index'])->name('entetes.index');
    Route::put('entetes/{id}', [EnteteController::class, 'update'])->name('entetes.update');

    // Gestion des clients
    Route::resource('clients/gestions_clients', ClientController::class);

    // Gestion des articles / produits
    Route::get('articles/impression', [ArticleController::class, 'printListe'])->name('articles.printListe');
    Route::resource('articles/gestions_articles', ArticleController::class);

    // Gestion des fournisseurs
    Route::resource('fournisseurs/gestions_fournisseurs', FournisseurController::class);

    // Gestion des entrées (Articles dans depots)
    Route::resource('gestions_entrees', EntreeController::class);
    Route::get('entrees/journal/impression', [EntreeController::class, 'printJournal'])->name('entrees.printJournal');
    Route::get('entrees/print/{id}', [EntreeController::class, 'printEntree'])->name('entrees.print');
    Route::post('entrees/import', [EntreeController::class, 'import'])->name('entrees.import');
    Route::get('entrees/template', [EntreeController::class, 'template'])->name('entrees.template');

    // Gestion des sorties (Sorties d'articles des depots)
    Route::resource('gestions_sorties', SortieController::class);
    Route::get('sorties/journal/impression', [SortieController::class, 'printJournal'])->name('sorties.printJournal');
    Route::get('sorties/print/{id}', [SortieController::class, 'printSortie'])->name('sorties.print');

    // Routes additionnelles pour les sorties
    Route::post('sorties/get-stock', [SortieController::class, 'getStock'])->name('sorties.getStock');

    // Routes additionnelles pour les ventes (doivent être avant la route resource)
    Route::post('ventes/get-stock', [VenteController::class, 'getStock'])->name('ventes.getStock');

    // Route des gestion des ventes
    Route::get('ventes/journal/impression', [VenteController::class, 'printJournal'])->name('ventes.printJournal');
    Route::get('ventes/periode/{periode}/impression', [VenteController::class, 'printPeriode'])->name('ventes.printPeriode');
    Route::get('ventes/non-soldees', [VenteController::class, 'nonSoldees'])->name('ventes.non-soldees');
    Route::post('ventes/{id}/solder', [VenteController::class, 'solder'])->name('ventes.solder');
    Route::get('ventes/{venteId}/paiements/{paiementId}/print', [VenteController::class, 'printPaiement'])->name('ventes.printPaiement');
    Route::resource('ventes/gestions_ventes', VenteController::class);
    Route::get('ventes/print/{id}', [VenteController::class, 'printVente'])->name('ventes.print');

    Route::get('bons-commandes/print/{id}', [BonCommandeController::class, 'print'])->name('bons_commandes.print');
    Route::post('bons-commandes/{id}/transformer', [BonCommandeController::class, 'transformer'])->name('bons_commandes.transformer');
    Route::resource('bons-commandes/gestions_bons_commandes', BonCommandeController::class);

    // Gestion des achats
    Route::get('achats/journal/impression', [AchatController::class, 'printJournal'])->name('achats.printJournal');
    Route::get('achats/non-soldes', [AchatController::class, 'nonSoldes'])->name('achats.non-soldes');
    Route::post('achats/{id}/solder', [AchatController::class, 'solder'])->name('achats.solder');
    Route::get('achats/{achatId}/paiements/{paiementId}/print', [AchatController::class, 'printPaiement'])->name('achats.printPaiement');
    Route::resource('achats/gestions_achats', AchatController::class);
    Route::get('achats/print/{id}', [AchatController::class, 'printAchat'])->name('achats.print');


    // Gestion des stocks (Consultation et ajustements)
    Route::resource('stocks/gestions_stocks', StockController::class);

    // Routes additionnelles pour les stocks
    Route::get('stocks/article/{articleId}', [StockController::class, 'showByArticle'])->name('stocks.by-article');
    Route::get('stocks/depot/{depotId}', [StockController::class, 'showByDepot'])->name('stocks.by-depot');
    Route::get('stocks/alertes', [StockController::class, 'alertes'])->name('stocks.alertes');

    // Gestion des transferts entre dépôts
    Route::resource('transferts/gestions_transferts', TransfertController::class);
    Route::get('transferts/print/{id}', [TransfertController::class, 'printTransfert'])->name('transferts.print');

    // Gestion des banques
    Route::get('banques/print/{id}', [BanqueController::class, 'printEtat'])->name('banques.print');
    Route::resource('banques/gestions_banques', BanqueController::class);

    // Gestion caisses
    Route::get('caisses/print/{id}', [CaisseController::class, 'printEtat'])->name('caisses.print');
    Route::resource('caisses/gestions_caisses', CaisseController::class);

    // Gestion des encaissements
    Route::get('encaissements/search-ventes', [EncaissementController::class, 'searchVentes'])->name('encaissements.searchVentes');
    Route::get('encaissements/vente-details/{id}', [EncaissementController::class, 'getVenteDetails'])->name('encaissements.venteDetails');
    Route::get('encaissements/unsettled', [EncaissementController::class, 'unsettled'])->name('gestions_encaissements.unsettled');
    Route::get('encaissements/settle/{id}', [EncaissementController::class, 'settle'])->name('gestions_encaissements.settle');
    Route::post('encaissements/process-settle/{id}', [EncaissementController::class, 'processSettle'])->name('gestions_encaissements.processSettle');
    Route::resource('encaissements/gestions_encaissements', EncaissementController::class);
    Route::get('encaissements/print/{id}', [EncaissementController::class, 'printEncaissement'])->name('encaissements.print');

    // Gestion des decaissements
    Route::get('decaissements/search-achats', [DecaissementController::class, 'searchAchats'])->name('decaissements.searchAchats');
    Route::get('decaissements/achat-details/{id}', [DecaissementController::class, 'getAchatDetails'])->name('decaissements.getAchatDetails');
    Route::get('decaissements/unsettled', [DecaissementController::class, 'unsettled'])->name('gestions_decaissements.unsettled');
    Route::get('decaissements/settle/{id}', [DecaissementController::class, 'settle'])->name('gestions_decaissements.settle');
    Route::post('decaissements/process-settle/{id}', [DecaissementController::class, 'processSettle'])->name('gestions_decaissements.processSettle');
    Route::resource('decaissements/gestions_decaissements', DecaissementController::class);
    Route::get('decaissements/print/{id}', [DecaissementController::class, 'printDecaissement'])->name('decaissements.print');

    // Gestion des depenses
    Route::get('depenses/journal/impression', [DepenseController::class, 'printJournal'])->name('depenses.printJournal');
    Route::resource('depenses/gestions_depenses', DepenseController::class)->except(['show']);

    // Route API pour récupérer le stock disponible (AJAX)
    Route::get('api/stock-disponible', [TransfertController::class, 'getStockDisponible'])->name('api.stock-disponible');

    // Routes de recherche pour Select2
    Route::get('clients/search', [ClientController::class, 'search'])->name('clients.search');
    Route::get('articles/search', [ArticleController::class, 'search'])->name('articles.search');
    Route::get('depots/search', [DepotController::class, 'search'])->name('depots.search');
    Route::get('fournisseurs/search', [FournisseurController::class, 'search'])->name('fournisseurs.search');

    // Routes pour les rapports de stock
    Route::prefix('rapports')->group(function () {
        Route::get('dashboard', [RapportStockController::class, 'dashboard'])->name('rapports.dashboard');
        Route::get('etat-stocks', [RapportStockController::class, 'etatStocks'])->name('rapports.etat-stocks');
        Route::get('alertes-stock', [RapportStockController::class, 'alertesStock'])->name('rapports.alertes-stock');
        Route::get('historique-mouvements', [RapportStockController::class, 'historiqueMouvements'])->name('rapports.historique-mouvements');
        Route::get('stocks-par-depot/{depotId?}', [RapportStockController::class, 'stocksParDepot'])->name('rapports.stocks-par-depot');
        Route::get('stocks-par-article/{articleId?}', [RapportStockController::class, 'stocksParArticle'])->name('rapports.stocks-par-article');
        Route::get('valorisation-stock', [RapportStockController::class, 'valorisationStock'])->name('rapports.valorisation-stock');
    });

    // Inventaires des stocks
    Route::post('inventaires/{id}/valider', [App\Http\Controllers\InventaireController::class, 'valider'])->name('gestions_inventaires.valider');
    Route::post('inventaires/{id}/annuler', [App\Http\Controllers\InventaireController::class, 'annuler'])->name('gestions_inventaires.annuler');
    Route::get('inventaires/{id}/print', [App\Http\Controllers\InventaireController::class, 'print'])->name('gestions_inventaires.print');
    Route::delete('inventaires/{id}/article/{detailId}', [App\Http\Controllers\InventaireController::class, 'removeArticle'])->name('gestions_inventaires.removeArticle');
    Route::resource('inventaires/gestions_inventaires', App\Http\Controllers\InventaireController::class);

    // Parametres et gestion des utilisateurs
    Route::resource('utilisateurs/gestions_utilisateurs', UserController::class);
    Route::get('profil', [UserController::class, 'profil'])->name('utilisateurs.profil');
    Route::put('profil/update/{id}', [UserController::class, 'updateProfil'])->name('utilisateurs.updateProfil');
    Route::put('utilisateurs/update_password/{id}', [UserController::class, 'updatePassword'])->name('utilisateurs.updatePassword');

    // gestion des parametres de l'application
    Route::get('parametres', [PageController::class, 'parametres'])->name('parametres');
    Route::put('parametres/update', [EnteteController::class, 'update'])->name('parametres.update');

    // Import de données
    // Import des clients
    Route::post('clients/import', [ClientController::class, 'import'])->name('clients.import');
    Route::get('clients/template', [ClientController::class, 'template'])->name('clients.template');

    // Import des fournisseurs
    Route::post('fournisseurs/import', [FournisseurController::class, 'import'])->name('fournisseurs.import');
    Route::get('fournisseurs/template', [FournisseurController::class, 'template'])->name('fournisseurs.template');

    // Import des articles
    Route::post('articles/import', [ArticleController::class, 'import'])->name('articles.import');
    Route::get('articles/template', [ArticleController::class, 'template'])->name('articles.template');

    // Gestion des stocks
    Route::post('stocks/import', [EntreeController::class, 'import'])->name('stocks.import');
    Route::post('stocks/reset', [EntreeController::class, 'reset'])->name('stocks.reset');
    Route::get('stocks/export', [EntreeController::class, 'export'])->name('stocks.export');

    // Maintenance
    Route::post('cache/clear', [PageController::class, 'clearCache'])->name('cache.clear');
    Route::post('logs/clear', [PageController::class, 'clearLogs'])->name('logs.clear');
    Route::post('migrate', [PageController::class, 'migrate'])->name('migrate');

    // Help
    Route::get('help', [PageController::class, 'help'])->name('help');

    // Génération des codes-barres pour les articles
    Route::get('articles/generate_code_barre', [PageController::class, 'codeBarArticle'])->name('articles.article_code_barre');
    Route::get('articles/print_code_barre', [PageController::class, 'printCodeBarArticle'])->name('articles.print_code_barre');
    });
});
