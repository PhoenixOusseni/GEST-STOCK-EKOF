<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\VenteDetail;
use App\Models\VentePaiement;
use App\Models\Client;
use App\Models\Article;
use App\Models\Depot;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\Support\Acces;
use App\Support\DepotActif;
use Illuminate\Support\Facades\DB;

class VenteController extends Controller
{
    public function index(Request $request)
    {
        $query = DepotActif::ventes()->with(['client', 'details']);

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('type_vente')) {
            $query->where('type_vente', $request->type_vente);
        }
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_vente', [$request->date_debut, $request->date_fin]);
        }
        if ($request->filled('numero_vente')) {
            $query->where('numero_vente', 'like', '%' . $request->numero_vente . '%');
        }

        $ventes = $query->orderBy('created_at', 'desc')->paginate(15);
        $clients = Client::orderBy('raison_sociale')->get();

        $stats = [
            'total_ventes'      => DepotActif::ventes()->count(),
            'montant_total'     => DepotActif::ventes()->sum('montant_total'),
            'ventes_mois'       => DepotActif::ventes()->whereMonth('date_vente', now()->month)->whereYear('date_vente', now()->year)->count(),
            'montant_mois'      => DepotActif::sommeVentes(now()->startOfMonth(), now()->endOfMonth()),
            'nb_clients_actifs' => DepotActif::ventes()->whereNotNull('client_id')->distinct('client_id')->count('client_id'),
        ];

        return view('pages.ventes.index', compact('ventes', 'clients', 'stats'));
    }

    public function create()
    {
        $clients = Client::orderBy('raison_sociale')->get();
        $articles = Article::orderBy('designation')->get();
        $depots = Depot::orderBy('designation')->get();
        return view('pages.ventes.create', compact('clients', 'articles', 'depots'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type_vente' => 'required|in:detail,gros',
            'depot_id' => 'required|exists:depots,id',
            'articles' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $this->verifierStockDepot($request->articles, $request->depot_id);

            $vente = Vente::create([
                'date_vente'      => $request->date_vente,
                'client_id'       => $request->client_id ?: null,
                'type_vente'      => $request->type_vente,
                'numero_vehicule' => $request->numero_vehicule,
                'chauffeur'       => $request->chauffeur,
            ]);

            $montantTotal = 0;
            foreach ($request->articles as $articleData) {
                $prixTotal = $articleData['quantite'] * $articleData['prix_vente'];
                $montantTotal += $prixTotal;
                VenteDetail::create([
                    'vente_id'   => $vente->id,
                    'article_id' => $articleData['article_id'],
                    'depot_id'   => $request->depot_id,
                    'quantite'   => $articleData['quantite'],
                    'prix_vente' => $articleData['prix_vente'],
                    'prix_total' => $prixTotal,
                ]);
                $stock = Stock::where('article_id', $articleData['article_id'])
                    ->where('depot_id', $request->depot_id)->first();
                $stock->decrement('quantite_disponible', $articleData['quantite']);
            }

            // Premier versement (optionnel)
            $montantPaye = floatval($request->montant_paye ?? 0);
            if ($montantPaye > 0) {
                VentePaiement::create([
                    'vente_id'       => $vente->id,
                    'montant'        => $montantPaye,
                    'date_paiement'  => $request->date_vente,
                    'mode_paiement'  => 'Versement initial',
                ]);
            }

            $resteAPayer = max(0, $montantTotal - $montantPaye);
            $vente->update([
                'montant_total'  => $montantTotal,
                'montant_paye'   => $montantPaye,
                'reste_a_payer'  => $resteAPayer,
                'statut'         => $resteAPayer <= 0 ? 'solde' : 'non_solde',
            ]);

            DB::commit();
            return redirect()->route('gestions_ventes.show', $vente->id)->with('success', 'Vente créée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function show(String $id)
    {
        $vente = DepotActif::ventes()->with(['client', 'details.article', 'details.depot', 'paiements'])->findOrFail($id);
        return view('pages.ventes.show', compact('vente'));
    }

    public function edit(String $id)
    {
        $vente = DepotActif::ventes()->findOrFail($id);
        if ($vente->statut === 'solde') {
            return redirect()->route('gestions_ventes.show', $vente->id)
                ->with('error', 'Impossible de modifier une vente soldée.');
        }
        $vente->load(['details.article', 'details.depot', 'paiements']);
        $clients = Client::orderBy('raison_sociale')->get();
        $articles = Article::orderBy('designation')->get();
        $depots = Depot::orderBy('designation')->get();
        return view('pages.ventes.edit', compact('vente', 'clients', 'articles', 'depots'));
    }

    public function update(Request $request, String $id)
    {
        $vente = Vente::with(['paiements', 'details'])->findOrFail($id);
        if ($vente->statut === 'solde') {
            return redirect()->route('gestions_ventes.show', $vente->id)
                ->with('error', 'Impossible de modifier une vente soldée.');
        }

        $request->validate([
            'type_vente' => 'required|in:detail,gros',
            'depot_id' => 'required|exists:depots,id',
            'articles' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            foreach ($vente->details as $old) {
                $stock = Stock::where('article_id', $old->article_id)
                    ->where('depot_id', $old->depot_id)->first();
                if ($stock) {
                    $stock->increment('quantite_disponible', $old->quantite);
                }
            }

            $this->verifierStockDepot($request->articles, $request->depot_id);

            $vente->update([
                'date_vente'      => $request->date_vente,
                'client_id'       => $request->client_id ?: null,
                'type_vente'      => $request->type_vente,
                'numero_vehicule' => $request->numero_vehicule,
                'chauffeur'       => $request->chauffeur,
            ]);

            $vente->details()->delete();

            $montantTotal = 0;
            foreach ($request->articles as $articleData) {
                $prixTotal = $articleData['quantite'] * $articleData['prix_vente'];
                $montantTotal += $prixTotal;
                VenteDetail::create([
                    'vente_id'   => $vente->id,
                    'article_id' => $articleData['article_id'],
                    'depot_id'   => $request->depot_id,
                    'quantite'   => $articleData['quantite'],
                    'prix_vente' => $articleData['prix_vente'],
                    'prix_total' => $prixTotal,
                ]);
                $stock = Stock::where('article_id', $articleData['article_id'])
                    ->where('depot_id', $request->depot_id)->first();
                $stock->decrement('quantite_disponible', $articleData['quantite']);
            }

            // Recalculer depuis les paiements existants
            $montantPaye = $vente->paiements()->sum('montant');
            $resteAPayer = max(0, $montantTotal - $montantPaye);
            $vente->update([
                'montant_total' => $montantTotal,
                'montant_paye'  => $montantPaye,
                'reste_a_payer' => $resteAPayer,
                'statut'        => $resteAPayer <= 0 ? 'solde' : 'non_solde',
            ]);

            DB::commit();
            return redirect()->route('gestions_ventes.show', $vente->id)->with('success', 'Vente modifiée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function destroy(String $id)
    {
        if (! Acces::estAdmin()) {
            abort(403);
        }

        $vente = DepotActif::ventes()->with(['details', 'encaissements.banque', 'encaissements.caisse'])->findOrFail($id);

        DB::beginTransaction();
        try {
            foreach ($vente->details as $detail) {
                $stock = Stock::where('article_id', $detail->article_id)
                    ->where('depot_id', $detail->depot_id)
                    ->first();
                if ($stock) {
                    $stock->increment('quantite_disponible', $detail->quantite);
                }
            }

            foreach ($vente->encaissements as $encaissement) {
                if ($encaissement->banque) {
                    $encaissement->banque->decrement('solde', $encaissement->montant_encaisse);
                }
                if ($encaissement->caisse) {
                    $encaissement->caisse->decrement('solde', $encaissement->montant_encaisse);
                }
                $encaissement->soldes()->delete();
                $encaissement->delete();
            }

            $vente->paiements()->delete();
            $vente->delete();
            DB::commit();

            return redirect()->route('gestions_ventes.index')->with('success', 'Vente supprimée avec succès. Les quantités ont été remises en stock.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('gestions_ventes.index')->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function getStock(Request $request)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id',
            'depot_id'   => 'required|exists:depots,id',
        ]);
        $stock = Stock::where('article_id', $request->article_id)
            ->where('depot_id', $request->depot_id)->first();
        if (!$stock) {
            return response()->json(['success' => false, 'quantite_disponible' => 0, 'prix_vente' => 0]);
        }
        $article = Article::find($request->article_id);
        return response()->json(['success' => true, 'quantite_disponible' => $stock->quantite_disponible, 'prix_vente' => $article->prix_vente]);
    }

    public function solder(Request $request, String $id)
    {
        $vente = Vente::findOrFail($id);

        if ($vente->statut === 'solde') {
            return redirect()->back()->with('error', 'Cette vente est déjà soldée.');
        }

        $request->validate([
            'montant'        => 'required|numeric|min:0.01|max:' . $vente->reste_a_payer,
            'date_paiement'  => 'required|date',
            'mode_paiement'  => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            VentePaiement::create([
                'vente_id'      => $vente->id,
                'montant'       => $request->montant,
                'date_paiement' => $request->date_paiement,
                'mode_paiement' => $request->mode_paiement,
                'notes'         => $request->notes,
            ]);

            $montantPaye = $vente->paiements()->sum('montant');
            $resteAPayer = max(0, $vente->montant_total - $montantPaye);
            $vente->update([
                'montant_paye'  => $montantPaye,
                'reste_a_payer' => $resteAPayer,
                'statut'        => $resteAPayer <= 0 ? 'solde' : 'non_solde',
            ]);

            DB::commit();

            $msg = $resteAPayer <= 0
                ? 'Vente soldée avec succès.'
                : 'Paiement de ' . number_format($request->montant, 0, ',', ' ') . ' FCFA enregistré. Reste : ' . number_format($resteAPayer, 0, ',', ' ') . ' FCFA.';

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function nonSoldees(Request $request)
    {
        $query = DepotActif::ventes()->with(['client', 'details'])->where('reste_a_payer', '>', 0);
        if ($request->filled('numero_vente')) {
            $query->where('numero_vente', 'like', '%' . $request->numero_vente . '%');
        }
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_vente', [$request->date_debut, $request->date_fin]);
        }
        $ventes = $query->orderBy('created_at', 'desc')->paginate(15);
        $totalReste = DepotActif::ventes()->where('reste_a_payer', '>', 0)->sum('reste_a_payer');
        return view('pages.ventes.non-soldees', compact('ventes', 'totalReste'));
    }

    public function printVente(String $id)
    {
        $vente = DepotActif::ventes()->with(['client', 'details.article', 'details.depot'])->findOrFail($id);
        return view('pages.ventes.print', compact('vente'));
    }

    public function printPaiement(String $venteId, String $paiementId)
    {
        $vente = DepotActif::ventes()->with(['client', 'paiements'])->findOrFail($venteId);
        $paiement = VentePaiement::where('vente_id', $venteId)->findOrFail($paiementId);
        return view('pages.ventes.print-paiement', compact('vente', 'paiement'));
    }

    private function verifierStockDepot(array $articles, $depotId): void
    {
        $demandes = [];
        foreach ($articles as $articleData) {
            $articleId = $articleData['article_id'] ?? null;
            if (!$articleId) {
                continue;
            }
            $demandes[$articleId] = ($demandes[$articleId] ?? 0) + (float) $articleData['quantite'];
        }

        $depot = Depot::find($depotId);
        foreach ($demandes as $articleId => $quantite) {
            $stock = Stock::where('article_id', $articleId)->where('depot_id', $depotId)->first();
            $disponible = $stock ? (float) $stock->quantite_disponible : 0;
            if ($disponible < $quantite) {
                $article = Article::find($articleId);
                $nom = $article->designation ?? 'article';
                $lieu = $depot->designation ?? 'magazin';
                throw new \Exception("Stock insuffisant pour {$nom} dans {$lieu}. Disponible: {$disponible}, demandé: {$quantite}");
            }
        }
    }

    public function printJournal()
    {
        $ventes = DepotActif::ventes()->with('client')->orderBy('date_vente')->orderBy('id')->get();

        return $this->etatVentes($ventes, 'Journal des ventes', 'Toutes les ventes du magazin', route('gestions_ventes.index'));
    }

    public function printPeriode(string $periode)
    {
        $periodes = [
            'jour' => [now()->startOfDay(), now()->endOfDay(), 'Ventes du jour', now()->translatedFormat('d F Y')],
            'semaine' => [now()->startOfWeek(), now()->endOfWeek(), 'Ventes de la semaine', 'Du ' . now()->startOfWeek()->format('d/m/Y') . ' au ' . now()->endOfWeek()->format('d/m/Y')],
            'mois' => [now()->startOfMonth(), now()->endOfMonth(), 'Ventes du mois', now()->translatedFormat('F Y')],
            'annee' => [now()->startOfYear(), now()->endOfYear(), 'Ventes de l\'année', now()->year],
        ];

        abort_unless(isset($periodes[$periode]), 404);
        [$debut, $fin, $titre, $libelle] = $periodes[$periode];

        $ventes = DepotActif::ventes()
            ->with('client')
            ->whereBetween('date_vente', [$debut->toDateString(), $fin->toDateString()])
            ->orderBy('date_vente')
            ->orderBy('id')
            ->get();

        return $this->etatVentes($ventes, $titre, $libelle, route('dashboard'));
    }

    private function etatVentes($ventes, string $titre, string $periode, string $backUrl)
    {
        $depot = DepotActif::current();
        $rows = $ventes->values()->map(function ($vente, $index) {
            $client = $vente->client ? ($vente->client->raison_sociale ?: $vente->client->nom) : 'Sans client';

            return [
                $index + 1,
                $vente->date_vente->format('d/m/Y'),
                $vente->numero_vente,
                $client,
                ucfirst($vente->type_vente ?? ''),
                number_format($vente->montant_total, 0, ',', ' '),
                number_format($vente->montant_paye, 0, ',', ' '),
                number_format($vente->reste_a_payer, 0, ',', ' '),
                $vente->statut === 'solde' ? 'Soldé' : 'Non soldé',
            ];
        });

        return view('pages.print.etat', [
            'docLabel' => $titre,
            'docNumber' => $depot->code ?? $depot->designation,
            'docDate' => now()->format('d/m/Y'),
            'backUrl' => $backUrl,
            'meta' => [
                ['Magazin', $depot->designation],
                ['Période', $periode],
                ['Factures', $ventes->count()],
            ],
            'columns' => [
                ['label' => '#', 'class' => 'inv-center', 'width' => '4%'],
                ['label' => 'Date', 'class' => 'inv-center', 'width' => '10%'],
                ['label' => 'N° facture', 'width' => '16%'],
                ['label' => 'Client', 'width' => '20%'],
                ['label' => 'Type', 'class' => 'inv-center', 'width' => '8%'],
                ['label' => 'Total', 'class' => 'inv-num', 'width' => '12%'],
                ['label' => 'Payé', 'class' => 'inv-num', 'width' => '12%'],
                ['label' => 'Reste', 'class' => 'inv-num', 'width' => '12%'],
                ['label' => 'Statut', 'class' => 'inv-center', 'width' => '8%'],
            ],
            'rows' => $rows,
            'totals' => [
                ['Payé', number_format($ventes->sum('montant_paye'), 0, ',', ' ') . ' F', 'strong'],
                ['Reste', number_format($ventes->sum('reste_a_payer'), 0, ',', ' ') . ' F', 'due'],
                ['Total', number_format($ventes->sum('montant_total'), 0, ',', ' ') . ' F'],
            ],
            'footerMessage' => $titre . ' — ' . $depot->designation,
        ]);
    }
}
