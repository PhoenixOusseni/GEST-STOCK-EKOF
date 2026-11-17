<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use App\Support\DepotActif;
use Illuminate\Http\Request;

class DepenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $this->depensesDuDepot();

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date_depense', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_depense', '<=', $request->date_fin);
        }

        $depenses = $query->orderBy('date_depense', 'desc')->paginate(15)->withQueryString();
        $categories = Depense::CATEGORIES;

        return view('pages.depenses.index', compact('depenses', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Depense::CATEGORIES;
        return view('pages.depenses.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date_depense' => 'required|date',
            'libelle' => 'required|string|max:255',
            'categorie' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'mode_paiement' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
            'observation' => 'nullable|string',
        ]);

        Depense::create($request->only([
            'date_depense',
            'libelle',
            'categorie',
            'montant',
            'mode_paiement',
            'reference',
            'observation',
        ]) + ['depot_id' => DepotActif::id()]);

        return redirect()->route('gestions_depenses.index')->with('success', 'Dépense ajoutée avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $depense = $this->trouver($id);
        $categories = Depense::CATEGORIES;
        return view('pages.depenses.edit', compact('depense', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $depense = $this->trouver($id);

        $request->validate([
            'date_depense' => 'required|date',
            'libelle' => 'required|string|max:255',
            'categorie' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'mode_paiement' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
            'observation' => 'nullable|string',
        ]);

        $depense->update($request->only([
            'date_depense',
            'libelle',
            'categorie',
            'montant',
            'mode_paiement',
            'reference',
            'observation',
        ]));

        return redirect()->route('gestions_depenses.index')->with('success', 'Dépense mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $depense = $this->trouver($id);
        $depense->delete();

        return redirect()->route('gestions_depenses.index')->with('success', 'Dépense supprimée avec succès.');
    }

    public function printJournal(Request $request)
    {
        $query = $this->depensesDuDepot();
        $docLabel = 'Journal des dépenses';
        $periodeLabel = 'Toutes les dépenses';

        $aujourdhui = now()->locale('fr');
        $periodes = [
            'jour' => [$aujourdhui->copy()->startOfDay(), $aujourdhui->copy()->endOfDay(), 'Dépenses du jour', $aujourdhui->translatedFormat('d F Y')],
            'semaine' => [$aujourdhui->copy()->startOfWeek(), $aujourdhui->copy()->endOfWeek(), 'Dépenses de la semaine', 'Du ' . $aujourdhui->copy()->startOfWeek()->format('d/m/Y') . ' au ' . $aujourdhui->copy()->endOfWeek()->format('d/m/Y')],
            'mois' => [$aujourdhui->copy()->startOfMonth(), $aujourdhui->copy()->endOfMonth(), 'Dépenses du mois', $aujourdhui->translatedFormat('F Y')],
            'annee' => [$aujourdhui->copy()->startOfYear(), $aujourdhui->copy()->endOfYear(), 'Dépenses de l\'année', (string) $aujourdhui->year],
        ];

        if ($request->filled('periode')) {
            abort_unless(isset($periodes[$request->periode]), 404);
            [$debut, $fin, $docLabel, $periodeLabel] = $periodes[$request->periode];
            $query->whereBetween('date_depense', [$debut->toDateString(), $fin->toDateString()]);
        } else {
            if ($request->filled('categorie')) {
                $query->where('categorie', $request->categorie);
            }
            if ($request->filled('date_debut')) {
                $query->whereDate('date_depense', '>=', $request->date_debut);
            }
            if ($request->filled('date_fin')) {
                $query->whereDate('date_depense', '<=', $request->date_fin);
            }
            $periodeLabel = $this->libellePeriode($request);
        }

        $depenses = $query->orderBy('date_depense')->orderBy('id')->get();
        $rows = $depenses->values()->map(function ($depense, $index) {
            return [
                $index + 1,
                $depense->date_depense->format('d/m/Y'),
                $depense->libelle,
                $depense->categorie,
                $depense->mode_paiement ?: '—',
                $depense->reference ?: '—',
                number_format($depense->montant, 0, ',', ' '),
            ];
        });

        $meta = [
            ['Période', $periodeLabel],
            ['Dépenses', $depenses->count()],
        ];
        if ($request->filled('categorie') && !$request->filled('periode')) {
            $meta[] = ['Catégorie', $request->categorie];
        }

        return view('pages.print.etat', [
            'docLabel' => $docLabel,
            'docNumber' => 'DEP/' . now()->format('Y/m'),
            'docDate' => now()->format('d/m/Y'),
            'backUrl' => route('gestions_depenses.index', $request->only(['categorie', 'date_debut', 'date_fin'])),
            'meta' => $meta,
            'columns' => [
                ['label' => '#', 'class' => 'inv-center', 'width' => '5%'],
                ['label' => 'Date', 'class' => 'inv-center', 'width' => '11%'],
                ['label' => 'Libellé', 'width' => '28%'],
                ['label' => 'Catégorie', 'width' => '16%'],
                ['label' => 'Mode', 'width' => '14%'],
                ['label' => 'Référence', 'width' => '12%'],
                ['label' => 'Montant', 'class' => 'inv-num', 'width' => '14%'],
            ],
            'rows' => $rows,
            'totals' => [
                ['Total des dépenses', number_format($depenses->sum('montant'), 0, ',', ' ') . ' FCFA'],
            ],
            'footerMessage' => $docLabel . ' — ' . $periodeLabel,
        ]);
    }

    private function depensesDuDepot()
    {
        return Depense::query()->where('depot_id', DepotActif::id());
    }

    private function trouver(string $id): Depense
    {
        return $this->depensesDuDepot()->findOrFail($id);
    }

    private function libellePeriode(Request $request): string
    {
        $debut = $request->date_debut;
        $fin = $request->date_fin;
        if ($debut && $fin) {
            return 'Du ' . date('d/m/Y', strtotime($debut)) . ' au ' . date('d/m/Y', strtotime($fin));
        }
        if ($debut) {
            return 'À partir du ' . date('d/m/Y', strtotime($debut));
        }
        if ($fin) {
            return 'Jusqu\'au ' . date('d/m/Y', strtotime($fin));
        }
        if ($request->filled('categorie')) {
            return $request->categorie;
        }

        return 'Toutes les dépenses';
    }
}
