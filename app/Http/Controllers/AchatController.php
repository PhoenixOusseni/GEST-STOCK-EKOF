<?php

namespace App\Http\Controllers;

use App\Models\Achat;
use App\Models\AchatDetail;
use App\Models\AchatPaiement;
use App\Models\Article;
use App\Models\Depot;
use App\Models\Fournisseur;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\Support\DepotActif;
use Illuminate\Support\Facades\DB;

class AchatController extends Controller
{
    public function index(Request $request)
    {
        $query = DepotActif::achats()->with(['fournisseur', 'details']);

        if ($request->filled('fournisseur_id')) {
            $query->where('fournisseur_id', $request->fournisseur_id);
        }
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_achat', [$request->date_debut, $request->date_fin]);
        }
        if ($request->filled('numero_achat')) {
            $query->where('numero_achat', 'like', '%' . $request->numero_achat . '%');
        }

        $achats = $query->orderBy('created_at', 'desc')->paginate(15);
        $fournisseurs = Fournisseur::orderBy('nom')->get();

        $stats = [
            'total_achats'           => DepotActif::achats()->count(),
            'montant_total'          => DepotActif::achats()->sum('montant_total'),
            'achats_mois'            => DepotActif::achats()->whereMonth('date_achat', now()->month)->whereYear('date_achat', now()->year)->count(),
            'montant_mois'           => DepotActif::sommeAchats(now()->startOfMonth(), now()->endOfMonth()),
            'nb_fournisseurs_actifs' => DepotActif::achats()->whereNotNull('fournisseur_id')->distinct('fournisseur_id')->count('fournisseur_id'),
        ];

        return view('pages.achats.index', compact('achats', 'fournisseurs', 'stats'));
    }

    public function create()
    {
        $fournisseurs = Fournisseur::orderBy('nom')->get();
        $articles = Article::orderBy('designation')->get();
        $depots = Depot::orderBy('designation')->get();
        return view('pages.achats.create', compact('fournisseurs', 'articles', 'depots'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_achat'               => 'required|date',
            'fournisseur_id'           => 'nullable|exists:fournisseurs,id',
            'depot_id'                 => 'required|exists:depots,id',
            'articles'                 => 'required|array|min:1',
            'articles.*.article_id'   => 'required|exists:articles,id',
            'articles.*.quantite'     => 'required|numeric|min:0.01',
            'articles.*.prix_unitaire'=> 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $achat = Achat::create([
                'date_achat'     => $request->date_achat,
                'fournisseur_id' => $request->fournisseur_id ?: null,
                'designation'    => $request->designation,
            ]);

            $montantTotal = 0;
            foreach ($request->articles as $articleData) {
                $prixTotal = $articleData['quantite'] * $articleData['prix_unitaire'];
                $montantTotal += $prixTotal;
                AchatDetail::create([
                    'achat_id'      => $achat->id,
                    'article_id'    => $articleData['article_id'],
                    'depot_id'      => $request->depot_id,
                    'quantite'      => $articleData['quantite'],
                    'prix_unitaire' => $articleData['prix_unitaire'],
                    'prix_total'    => $prixTotal,
                ]);
                $stock = Stock::firstOrCreate(
                    ['article_id' => $articleData['article_id'], 'depot_id' => $request->depot_id],
                    ['quantite_disponible' => 0, 'quantite_reserve' => 0, 'quantite_minimale' => 0]
                );
                $stock->increment('quantite_disponible', $articleData['quantite']);
            }

            // Premier versement (optionnel)
            $montantPaye = floatval($request->montant_paye ?? 0);
            if ($montantPaye > 0) {
                AchatPaiement::create([
                    'achat_id'      => $achat->id,
                    'montant'       => $montantPaye,
                    'date_paiement' => $request->date_achat,
                    'mode_paiement' => 'Versement initial',
                ]);
            }

            $resteAPayer = max(0, $montantTotal - $montantPaye);
            $achat->update([
                'montant_total' => $montantTotal,
                'montant_paye'  => $montantPaye,
                'reste_a_payer' => $resteAPayer,
                'statut'        => $resteAPayer <= 0 ? 'solde' : 'non_solde',
            ]);

            DB::commit();
            return redirect()->route('gestions_achats.show', $achat->id)->with('success', 'Achat créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function show(String $id)
    {
        $achat = DepotActif::achats()->with(['fournisseur', 'details.article', 'details.depot', 'paiements'])->findOrFail($id);
        return view('pages.achats.show', compact('achat'));
    }

    public function printAchat(String $id)
    {
        $achat = DepotActif::achats()->with(['fournisseur', 'details.article', 'details.depot'])->findOrFail($id);
        return view('pages.achats.print', compact('achat'));
    }

    public function edit(String $id)
    {
        $achat = Achat::findOrFail($id);
        if ($achat->statut === 'solde') {
            return redirect()->route('gestions_achats.show', $achat->id)
                ->with('error', 'Impossible de modifier un achat soldé.');
        }
        $achat->load(['fournisseur', 'details.article', 'details.depot', 'paiements']);
        $fournisseurs = Fournisseur::orderBy('nom')->get();
        $articles = Article::orderBy('designation')->get();
        $depots = Depot::orderBy('designation')->get();
        return view('pages.achats.edit', compact('achat', 'fournisseurs', 'articles', 'depots'));
    }

    public function update(Request $request, String $id)
    {
        $request->validate([
            'date_achat'               => 'required|date',
            'fournisseur_id'           => 'nullable|exists:fournisseurs,id',
            'depot_id'                 => 'required|exists:depots,id',
            'articles'                 => 'required|array|min:1',
            'articles.*.article_id'   => 'required|exists:articles,id',
            'articles.*.quantite'     => 'required|numeric|min:0.01',
            'articles.*.prix_unitaire'=> 'required|numeric|min:0',
        ]);

        $achat = Achat::with(['details', 'paiements'])->findOrFail($id);
        if ($achat->statut === 'solde') {
            return redirect()->route('gestions_achats.show', $achat->id)
                ->with('error', 'Impossible de modifier un achat soldé.');
        }

        DB::beginTransaction();
        try {
            foreach ($achat->details as $detail) {
                $stock = Stock::where('article_id', $detail->article_id)->where('depot_id', $detail->depot_id)->first();
                if ($stock) $stock->decrement('quantite_disponible', $detail->quantite);
            }
            $achat->details()->delete();

            $achat->update([
                'date_achat'     => $request->date_achat,
                'fournisseur_id' => $request->fournisseur_id ?: null,
                'designation'    => $request->designation,
            ]);

            $montantTotal = 0;
            foreach ($request->articles as $articleData) {
                $prixTotal = $articleData['quantite'] * $articleData['prix_unitaire'];
                $montantTotal += $prixTotal;
                AchatDetail::create([
                    'achat_id'      => $achat->id,
                    'article_id'    => $articleData['article_id'],
                    'depot_id'      => $request->depot_id,
                    'quantite'      => $articleData['quantite'],
                    'prix_unitaire' => $articleData['prix_unitaire'],
                    'prix_total'    => $prixTotal,
                ]);
                $stock = Stock::firstOrCreate(
                    ['article_id' => $articleData['article_id'], 'depot_id' => $request->depot_id],
                    ['quantite_disponible' => 0, 'quantite_reserve' => 0, 'quantite_minimale' => 0]
                );
                $stock->increment('quantite_disponible', $articleData['quantite']);
            }

            $montantPaye = $achat->paiements()->sum('montant');
            $resteAPayer = max(0, $montantTotal - $montantPaye);
            $achat->update([
                'montant_total' => $montantTotal,
                'montant_paye'  => $montantPaye,
                'reste_a_payer' => $resteAPayer,
                'statut'        => $resteAPayer <= 0 ? 'solde' : 'non_solde',
            ]);

            DB::commit();
            return redirect()->route('gestions_achats.show', $achat->id)->with('success', 'Achat mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function destroy(String $id)
    {
        $achat = Achat::with('details')->findOrFail($id);
        if ($achat->statut === 'solde') {
            return redirect()->route('gestions_achats.index')
                ->with('error', 'Impossible de supprimer un achat soldé.');
        }
        DB::beginTransaction();
        try {
            foreach ($achat->details as $detail) {
                $stock = Stock::where('article_id', $detail->article_id)->where('depot_id', $detail->depot_id)->first();
                if ($stock) $stock->decrement('quantite_disponible', $detail->quantite);
            }
            $achat->delete();
            DB::commit();
            return redirect()->route('gestions_achats.index')->with('success', 'Achat supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('gestions_achats.index')->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function solder(Request $request, String $id)
    {
        $achat = Achat::findOrFail($id);

        if ($achat->statut === 'solde') {
            return redirect()->back()->with('error', 'Cet achat est déjà soldé.');
        }

        $request->validate([
            'montant'       => 'required|numeric|min:0.01|max:' . $achat->reste_a_payer,
            'date_paiement' => 'required|date',
            'mode_paiement' => 'nullable|string|max:100',
            'notes'         => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            AchatPaiement::create([
                'achat_id'      => $achat->id,
                'montant'       => $request->montant,
                'date_paiement' => $request->date_paiement,
                'mode_paiement' => $request->mode_paiement,
                'notes'         => $request->notes,
            ]);

            $montantPaye = $achat->paiements()->sum('montant');
            $resteAPayer = max(0, $achat->montant_total - $montantPaye);
            $achat->update([
                'montant_paye'  => $montantPaye,
                'reste_a_payer' => $resteAPayer,
                'statut'        => $resteAPayer <= 0 ? 'solde' : 'non_solde',
            ]);

            DB::commit();

            $msg = $resteAPayer <= 0
                ? 'Achat soldé avec succès.'
                : 'Paiement de ' . number_format($request->montant, 0, ',', ' ') . ' FCFA enregistré. Reste : ' . number_format($resteAPayer, 0, ',', ' ') . ' FCFA.';

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function nonSoldes(Request $request)
    {
        $query = DepotActif::achats()->with(['fournisseur', 'details'])->where('reste_a_payer', '>', 0);
        if ($request->filled('numero_achat')) {
            $query->where('numero_achat', 'like', '%' . $request->numero_achat . '%');
        }
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_achat', [$request->date_debut, $request->date_fin]);
        }
        $achats = $query->orderBy('created_at', 'desc')->paginate(15);
        $totalReste = DepotActif::achats()->where('reste_a_payer', '>', 0)->sum('reste_a_payer');
        return view('pages.achats.non-soldes', compact('achats', 'totalReste'));
    }

    public function printPaiement(String $achatId, String $paiementId)
    {
        $achat = Achat::with(['fournisseur', 'paiements'])->findOrFail($achatId);
        $paiement = AchatPaiement::where('achat_id', $achatId)->findOrFail($paiementId);
        return view('pages.achats.print-paiement', compact('achat', 'paiement'));
    }

    public function printJournal()
    {
        $achats = DepotActif::achats()->with('fournisseur')->orderBy('date_achat')->orderBy('id')->get();
        $depot = DepotActif::current();
        $rows = $achats->values()->map(function ($achat, $index) {
            $fournisseur = $achat->fournisseur ? ($achat->fournisseur->raison_sociale ?: $achat->fournisseur->nom) : 'Sans fournisseur';

            return [
                $index + 1,
                $achat->date_achat->format('d/m/Y'),
                $achat->numero_achat,
                $fournisseur,
                number_format($achat->montant_total, 0, ',', ' '),
                number_format($achat->montant_paye, 0, ',', ' '),
                number_format($achat->reste_a_payer, 0, ',', ' '),
                $achat->statut === 'solde' ? 'Soldé' : 'Non soldé',
            ];
        });

        return view('pages.print.etat', [
            'docLabel' => 'Journal des achats',
            'docNumber' => $depot->code ?? $depot->designation,
            'docDate' => now()->format('d/m/Y'),
            'backUrl' => route('gestions_achats.index'),
            'meta' => [
                ['Magazin', $depot->designation],
                ['Pièces', $achats->count()],
            ],
            'columns' => [
                ['label' => '#', 'class' => 'inv-center', 'width' => '5%'],
                ['label' => 'Date', 'class' => 'inv-center', 'width' => '12%'],
                ['label' => 'N° achat', 'width' => '16%'],
                ['label' => 'Fournisseur', 'width' => '23%'],
                ['label' => 'Total', 'class' => 'inv-num', 'width' => '12%'],
                ['label' => 'Payé', 'class' => 'inv-num', 'width' => '12%'],
                ['label' => 'Reste', 'class' => 'inv-num', 'width' => '12%'],
                ['label' => 'Statut', 'class' => 'inv-center', 'width' => '8%'],
            ],
            'rows' => $rows,
            'totals' => [
                ['Payé', number_format($achats->sum('montant_paye'), 0, ',', ' ') . ' F', 'strong'],
                ['Reste', number_format($achats->sum('reste_a_payer'), 0, ',', ' ') . ' F', 'due'],
                ['Total', number_format($achats->sum('montant_total'), 0, ',', ' ') . ' F'],
            ],
            'footerMessage' => 'Journal des achats — ' . $depot->designation,
        ]);
    }
}
