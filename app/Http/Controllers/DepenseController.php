<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use Illuminate\Http\Request;

class DepenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Depense::query();

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
        ]));

        return redirect()->route('gestions_depenses.index')->with('success', 'Dépense ajoutée avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $depense = Depense::findOrFail($id);
        $categories = Depense::CATEGORIES;
        return view('pages.depenses.edit', compact('depense', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $depense = Depense::findOrFail($id);

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
        $depense = Depense::findOrFail($id);
        $depense->delete();

        return redirect()->route('gestions_depenses.index')->with('success', 'Dépense supprimée avec succès.');
    }
}
