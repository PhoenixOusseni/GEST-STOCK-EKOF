<?php

namespace App\Http\Controllers;

use App\Models\Depot;
use App\Models\EntreeDetail;
use App\Models\VenteDetail;
use App\Support\Acces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $depots = Depot::query()
            ->when(Acces::estMagazin(), fn ($q) => $q->whereKey(auth()->user()->depot_id))
            ->withCount(['stocks as articles_en_stock' => fn ($q) => $q->where('quantite_disponible', '>', 0)])
            ->withSum('stocks as quantite_totale', 'quantite_disponible')
            ->orderBy('designation')
            ->get();

        $ventesMois = VenteDetail::query()
            ->select('depot_id', DB::raw('SUM(prix_total) as total'))
            ->whereHas('vente', fn ($q) => $q->where('date_vente', '>=', now()->startOfMonth()->toDateString()))
            ->groupBy('depot_id')
            ->pluck('total', 'depot_id');

        return view('pages.depots.index', compact('depots', 'ventesMois'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.depots.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Création du depot
        $depot = new Depot();
        $code = 'DPT-' . str_pad(Depot::max('id') + 1, 5, '0', STR_PAD_LEFT);
        $depot->code = $code;
        $depot->designation = $request->designation;
        $depot->localisation = $request->localisation;
        $depot->responsable = $request->responsable;
        $depot->contact = $request->contact;
        $depot->save();

        // Redirection avec un message de succès
        return redirect()->route('gestions_depots.index')->with('success', 'Magazin ajouté avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id)
    {
        // Récupération du depot à afficher
        $depot = Depot::findOrFail($id);
        // Charger les stocks avec les articles et les détails d'entrée
        $depot->load([
            'stocks.article',
            'entreesDetails.entree'
        ]);

        // Récupérer les articles avec leurs derniers mouvements
        $articlesAvecDetails = $depot->stocks()->with(['article', 'depot'])->get()->map(function ($stock) {
            // Chercher la dernière entrée pour cet article dans ce magazin
            $lastEntree = EntreeDetail::where('article_id', $stock->article_id)
                ->where('depot_id', $stock->depot_id)
                ->with('entree')
                ->latest('created_at')
                ->first();

            // Total des quantités entrées (achats) pour cet article dans ce magazin
            $totalEntree = \App\Models\MouvementStock::where('article_id', $stock->article_id)
                ->where('depot_id', $stock->depot_id)
                ->where('type_mouvement', 'entree')
                ->sum('quantite');

            // Total des quantités sorties (ventes) pour cet article dans ce magazin
            $totalSortie = \App\Models\MouvementStock::where('article_id', $stock->article_id)
                ->where('depot_id', $stock->depot_id)
                ->where('type_mouvement', 'sortie')
                ->sum('quantite');

            return [
                'stock' => $stock,
                'lastEntree' => $lastEntree,
                'totalEntree' => $totalEntree,
                'totalSortie' => $totalSortie,
            ];
        });

        return view('pages.depots.show', compact('depot', 'articlesAvecDetails'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(String $id)
    {
        // Récupération du depot à éditer
        $depotFinds = Depot::findOrFail($id);
        return view('pages.depots.edit', compact('depotFinds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $id)
    {
        // Récupération du depot à mettre à jour
        $depot = Depot::findOrFail($id);
        // Mise à jour des champs
        $depot->designation = $request->designation;
        $depot->localisation = $request->localisation;
        $depot->responsable = $request->responsable;
        $depot->contact = $request->contact;
        // Sauvegarde des modifications
        $depot->save();
        // Redirection avec un message de succès
        return redirect()->route('gestions_depots.index')->with('success', 'Magazin mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id)
    {
        // Récupération du depot à supprimer
        $depot = Depot::findOrFail($id);
        // Suppression du depot
        $depot->delete();
        // Redirection avec un message de succès
        return redirect()->route('gestions_depots.index')->with('success', 'Magazin supprimé avec succès.');
    }

    /**
     * Import depots from CSV/Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240'
        ]);

        try {
            $file = $request->file('file');
            // Implementation to be added for CSV/Excel parsing
            // Using Laravel Excel or similar library

            return redirect()->back()->with('success', 'Magazins importés avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    /**
     * Download template for depot import
     */
    public function template()
    {
        $headers = ['Code', 'Désignation', 'Localisation', 'Responsable', 'Contact'];
        $filename = 'template_depots.csv';

        return response()->streamDownload(function() use ($headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            fclose($handle);
        }, $filename);
    }

    /**
     * Search depots for Select2 AJAX
     */
    public function search(Request $request)
    {
        $search = $request->get('search', '');

        $depots = Depot::where(function($query) use ($search) {
            $query->where('designation', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('localisation', 'like', "%{$search}%");
        })
        ->limit(20)
        ->get()
        ->map(function($depot) {
            return [
                'id' => $depot->id,
                'designation' => $depot->designation,
                'code' => $depot->code,
                'localisation' => $depot->localisation
            ];
        });

        return response()->json($depots);
    }
}
