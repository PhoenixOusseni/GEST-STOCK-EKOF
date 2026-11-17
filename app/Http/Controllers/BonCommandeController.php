<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\BonCommande;
use App\Models\BonCommandeDetail;
use App\Models\Client;
use App\Models\Depot;
use App\Models\Stock;
use App\Models\Vente;
use App\Models\VenteDetail;
use App\Models\VentePaiement;
use App\Support\DepotActif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BonCommandeController extends Controller
{
    public function index(Request $request)
    {
        $query = DepotActif::bonsCommandes()->with(['client', 'vente']);

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_commande', [$request->date_debut, $request->date_fin]);
        }
        if ($request->filled('numero_bon')) {
            $query->where('numero_bon', 'like', '%' . $request->numero_bon . '%');
        }

        $bons = $query->orderByDesc('id')->paginate(15)->withQueryString();
        $clients = Client::orderBy('raison_sociale')->get();
        $stats = [
            'total' => DepotActif::bonsCommandes()->count(),
            'en_cours' => DepotActif::bonsCommandes()->where('statut', 'en_cours')->count(),
            'valides' => DepotActif::bonsCommandes()->where('statut', 'valide')->count(),
            'montant_en_cours' => DepotActif::bonsCommandes()->where('statut', 'en_cours')->sum('montant_total'),
        ];

        return view('pages.bons-commandes.index', compact('bons', 'clients', 'stats'));
    }

    public function create()
    {
        $clients = Client::orderBy('raison_sociale')->get();
        $articles = Article::orderBy('designation')->get();
        $depots = Depot::orderBy('designation')->get();

        return view('pages.bons-commandes.create', compact('clients', 'articles', 'depots'));
    }

    public function store(Request $request)
    {
        $data = $this->valider($request);

        DB::beginTransaction();
        try {
            $bon = BonCommande::create([
                'date_commande' => $data['date_commande'],
                'client_id' => $data['client_id'],
                'type_vente' => $data['type_vente'],
                'statut' => 'en_cours',
            ]);

            $montantTotal = $this->enregistrerLignes($bon, $data['articles'], $data['depot_id']);
            $bon->update([
                'montant_total' => $montantTotal,
                'montant_paye' => 0,
                'reste_a_payer' => $montantTotal,
            ]);

            DB::commit();

            return redirect()->route('gestions_bons_commandes.show', $bon->id)
                ->with('success', 'Bon de commande enregistré. Le stock n\'a pas été modifié.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $bon = $this->trouver($id);

        return view('pages.bons-commandes.show', compact('bon'));
    }

    public function edit(string $id)
    {
        $bon = $this->trouver($id);
        if ($bon->estValide()) {
            return redirect()->route('gestions_bons_commandes.show', $bon->id)
                ->with('error', 'Un bon validé ne peut plus être modifié.');
        }

        $clients = Client::orderBy('raison_sociale')->get();
        $articles = Article::orderBy('designation')->get();
        $depots = Depot::orderBy('designation')->get();

        return view('pages.bons-commandes.edit', compact('bon', 'clients', 'articles', 'depots'));
    }

    public function update(Request $request, string $id)
    {
        $bon = $this->trouver($id);
        if ($bon->estValide()) {
            return redirect()->route('gestions_bons_commandes.show', $bon->id)
                ->with('error', 'Un bon validé ne peut plus être modifié.');
        }

        $data = $this->valider($request);

        DB::beginTransaction();
        try {
            $bon->update([
                'date_commande' => $data['date_commande'],
                'client_id' => $data['client_id'],
                'type_vente' => $data['type_vente'],
            ]);
            $bon->details()->delete();
            $montantTotal = $this->enregistrerLignes($bon, $data['articles'], $data['depot_id']);
            $bon->update([
                'montant_total' => $montantTotal,
                'montant_paye' => 0,
                'reste_a_payer' => $montantTotal,
            ]);

            DB::commit();

            return redirect()->route('gestions_bons_commandes.show', $bon->id)
                ->with('success', 'Bon de commande mis à jour.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $bon = $this->trouver($id);
        if ($bon->estValide()) {
            return redirect()->route('gestions_bons_commandes.index')
                ->with('error', 'Un bon validé ne peut pas être supprimé.');
        }

        $bon->delete();

        return redirect()->route('gestions_bons_commandes.index')
            ->with('success', 'Bon de commande supprimé.');
    }

    public function transformer(Request $request, string $id)
    {
        $request->validate([
            'montant_paye' => 'nullable|numeric|min:0',
            'date_paiement' => 'nullable|date',
            'mode_paiement' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $bon = BonCommande::with('details')->lockForUpdate()->findOrFail($id);
            if ($bon->estValide()) {
                throw new \Exception('Ce bon de commande est déjà validé.');
            }

            $depotId = DepotActif::id();
            if ($bon->details->contains(fn ($detail) => (int) $detail->depot_id !== (int) $depotId)) {
                throw new \Exception('Ce bon ne concerne pas le magazin actif.');
            }

            $articles = $bon->details->map(fn ($detail) => [
                'article_id' => $detail->article_id,
                'quantite' => $detail->quantite,
            ])->all();
            $this->verifierStockDepot($articles, $depotId);

            $vente = Vente::create([
                'date_vente' => now()->toDateString(),
                'client_id' => $bon->client_id,
                'type_vente' => $bon->type_vente,
            ]);

            $montantTotal = 0;
            foreach ($bon->details as $detail) {
                $prixTotal = $detail->quantite * $detail->prix_vente;
                $montantTotal += $prixTotal;
                VenteDetail::create([
                    'vente_id' => $vente->id,
                    'article_id' => $detail->article_id,
                    'depot_id' => $detail->depot_id,
                    'quantite' => $detail->quantite,
                    'prix_vente' => $detail->prix_vente,
                    'prix_total' => $prixTotal,
                ]);
                $stock = Stock::where('article_id', $detail->article_id)
                    ->where('depot_id', $detail->depot_id)
                    ->first();
                $stock->decrement('quantite_disponible', $detail->quantite);
            }

            $montantPaye = (float) ($request->montant_paye ?? 0);
            if ($montantPaye > $montantTotal) {
                throw new \Exception('Le versement ne peut pas dépasser le montant de la facture.');
            }
            if ($montantPaye > 0) {
                VentePaiement::create([
                    'vente_id' => $vente->id,
                    'montant' => $montantPaye,
                    'date_paiement' => $request->date_paiement ?: now()->toDateString(),
                    'mode_paiement' => $request->mode_paiement ?: 'Espèces',
                ]);
            }

            $reste = max(0, $montantTotal - $montantPaye);
            $vente->update([
                'montant_total' => $montantTotal,
                'montant_paye' => $montantPaye,
                'reste_a_payer' => $reste,
                'statut' => $reste <= 0 ? 'solde' : 'non_solde',
            ]);

            $bon->update([
                'statut' => 'valide',
                'vente_id' => $vente->id,
            ]);

            DB::commit();

            return redirect()->route('gestions_ventes.show', $vente->id)
                ->with('success', 'Bon ' . $bon->numero_bon . ' validé. La facture ' . $vente->numero_vente . ' a été créée et le stock a été diminué.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function print(string $id)
    {
        $bon = $this->trouver($id);

        return view('pages.bons-commandes.print', compact('bon'));
    }

    private function trouver(string $id): BonCommande
    {
        $bon = DepotActif::bonsCommandes()
            ->with(['client', 'details.article', 'details.depot', 'vente'])
            ->findOrFail($id);

        return $bon;
    }

    private function valider(Request $request): array
    {
        return $request->validate([
            'date_commande' => 'required|date',
            'client_id' => 'nullable|exists:clients,id',
            'type_vente' => 'required|in:detail,gros',
            'depot_id' => 'required|exists:depots,id',
            'articles' => 'required|array|min:1',
            'articles.*.article_id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|numeric|min:0.01',
            'articles.*.prix_vente' => 'required|numeric|min:0',
        ]);
    }

    private function enregistrerLignes(BonCommande $bon, array $articles, $depotId): float
    {
        $montantTotal = 0;
        foreach ($articles as $article) {
            $prixTotal = $article['quantite'] * $article['prix_vente'];
            $montantTotal += $prixTotal;
            BonCommandeDetail::create([
                'bon_commande_id' => $bon->id,
                'article_id' => $article['article_id'],
                'depot_id' => $depotId,
                'quantite' => $article['quantite'],
                'prix_vente' => $article['prix_vente'],
                'prix_total' => $prixTotal,
            ]);
        }

        return $montantTotal;
    }

    private function verifierStockDepot(array $articles, $depotId): void
    {
        $demandes = [];
        foreach ($articles as $article) {
            $articleId = $article['article_id'] ?? null;
            if (!$articleId) {
                continue;
            }
            $demandes[$articleId] = ($demandes[$articleId] ?? 0) + (float) $article['quantite'];
        }

        $depot = Depot::find($depotId);
        foreach ($demandes as $articleId => $quantite) {
            $stock = Stock::where('article_id', $articleId)->where('depot_id', $depotId)->first();
            $disponible = $stock ? (float) $stock->quantite_disponible : 0;
            if ($disponible < $quantite) {
                $nom = Article::find($articleId)->designation ?? 'article';
                $lieu = $depot->designation ?? 'magazin';
                throw new \Exception("Stock insuffisant pour {$nom} dans {$lieu}. Disponible : {$disponible}, demandé : {$quantite}. Le bon reste en cours.");
            }
        }
    }
}
