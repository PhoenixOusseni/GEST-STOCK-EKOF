<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Depot;
use App\Support\DepotActif;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $depots = Depot::orderBy('designation')->get();
        $depotId = $request->get('depot_id');

        $query = Article::with('stocks.depot');

        if ($depotId) {
            $query->whereHas('stocks', fn($q) => $q->where('depot_id', $depotId));
        }

        $articles = $query->get();
        $selectedDepot = $depotId ? $depots->find($depotId) : null;

        return view('pages.articles.index', compact('articles', 'depots', 'selectedDepot', 'depotId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.articles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'designation'   => 'required|array|min:1',
            'designation.*' => 'required|string|max:255',
            'prix_achat'    => 'required|array|min:1',
            'prix_achat.*'  => 'required|numeric|min:0',
            'prix_vente'    => 'required|array|min:1',
            'prix_vente.*'  => 'required|numeric|min:0',
            'stock.*'       => 'nullable|numeric|min:0',
            'seuil.*'       => 'nullable|numeric|min:0',
        ]);

        $designations = $request->input('designation');
        $count = count($designations);

        for ($i = 0; $i < $count; $i++) {
            $article = new Article();
            $article->code        = 'ART-' . str_pad(Article::max('id') + 1, 5, '0', STR_PAD_LEFT);
            $article->designation = $designations[$i];
            $article->prix_achat  = $request->input('prix_achat')[$i];
            $article->prix_vente  = $request->input('prix_vente')[$i];
            $article->stock       = $request->input('stock')[$i] ?? 0;
            $article->seuil       = $request->input('seuil')[$i] ?? 10;
            $article->save();
        }

        $msg = $count === 1 ? 'Article ajouté avec succès.' : "$count articles ajoutés avec succès.";
        return redirect()->route('gestions_articles.index')->with('success', $msg);
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(String $id)
    {
        $articleFinds = Article::findOrFail($id);
        return view('pages.articles.edit', compact('articleFinds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $id)
    {
        // Récupération de l'article à mettre à jour
        $article = Article::findOrFail($id);
        // Mise à jour des champs
        $article->designation = $request->designation;
        $article->prix_achat = $request->prix_achat;
        $article->prix_vente = $request->prix_vente;
        $article->seuil = $request->seuil;
        $article->stock = $request->stock;
        // Sauvegarde des modifications
        $article->save();
        // Redirection avec un message de succès
        return redirect()->route('gestions_articles.index')->with('success', 'Article mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id)
    {
        $article = Article::findOrFail($id);
        $article->delete();
        return redirect()->route('gestions_articles.index')->with('success', 'Article supprimé avec succès.');
    }

    /**
     * Import articles from CSV/Excel file
     */
    public function import(Request $request)
    {
        try {
            // Validation du fichier
            $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:2048'
            ]);

            $file = $request->file('file');

            // Ouvrir le fichier CSV
            $handle = fopen($file->getRealPath(), 'r');

            // Détecter le séparateur (virgule ou point-virgule)
            $firstLine = fgets($handle);
            rewind($handle);

            $delimiter = ',';
            if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $delimiter = ';';
            }

            // Ignorer la première ligne (en-têtes)
            $header = fgetcsv($handle, 1000, $delimiter);

            $imported = 0;
            $errors = [];

            // Obtenir le dernier ID pour la génération des codes
            $lastId = Article::max('id') ?? 0;

            // Parcourir chaque ligne du fichier
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                // Ignorer les lignes complètement vides (toutes les colonnes vides)
                $hasData = false;
                foreach ($row as $cell) {
                    if (!empty(trim($cell))) {
                        $hasData = true;
                        break;
                    }
                }

                if (!$hasData) {
                    continue;
                }

                try {
                    $designation = trim($row[0] ?? '');
                    $prix_achat = floatval(str_replace(',', '.', trim($row[1] ?? '0')));
                    $prix_vente = floatval(str_replace(',', '.', trim($row[2] ?? '0')));

                    // Générer un code automatique unique
                    $lastId++;
                    $code = 'ART-' . str_pad($lastId, 5, '0', STR_PAD_LEFT);

                    // Vérifier si le code existe déjà (sécurité)
                    while (Article::where('code', $code)->exists()) {
                        $lastId++;
                        $code = 'ART-' . str_pad($lastId, 5, '0', STR_PAD_LEFT);
                    }

                    // Créer un nouveau article
                    Article::create([
                        'code' => $code,
                        'designation' => $designation,
                        'prix_achat' => $prix_achat,
                        'prix_vente' => $prix_vente,
                        'seuil' => 10,
                    ]);

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Ligne " . ($imported + 2) . ": " . $e->getMessage() . " | Données: " . implode(' | ', $row);
                }
            }

            fclose($handle);

            // Message de succès avec détails
            $message = "$imported article(s) importé(s) avec succès.";

            if (!empty($errors)) {
                $message .= " " . count($errors) . " erreur(s) détectée(s).";
                // Afficher les erreurs dans la session
                session()->flash('errors_detail', $errors);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    /**
     * Download template for article import
     */
    public function template()
    {
        $headers = ['Désignation', 'Prix Achat', 'Prix Vente', 'Seuil'];
        $filename = 'template_articles.csv';

        return response()->streamDownload(function() use ($headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            fclose($handle);
        }, $filename);
    }

    /**
     * Search articles for Select2 AJAX
     */
    public function search(Request $request)
    {
        $search = $request->get('search', '');

        $articles = Article::where(function($query) use ($search) {
            $query->where('designation', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        })
        ->limit(20)
        ->get()
        ->map(function($article) {
            return [
                'id' => $article->id,
                'designation' => $article->designation,
                'code' => $article->code,
                'prix_achat' => $article->prix_achat,
                'prix_vente' => $article->prix_vente
            ];
        });

        return response()->json($articles);
    }

    public function printListe()
    {
        $depot = DepotActif::current();
        $articles = Article::with(['stocks' => fn ($q) => $q->where('depot_id', $depot->id)])
            ->orderBy('designation')
            ->get();

        $rows = $articles->values()->map(function ($article, $index) {
            $quantite = $article->stocks->sum('quantite_disponible');

            return [
                $index + 1,
                $article->code,
                $article->designation,
                number_format($article->prix_achat, 0, ',', ' '),
                number_format($article->prix_vente, 0, ',', ' '),
                number_format($quantite, 0, ',', ' '),
                number_format($article->seuil ?? 0, 0, ',', ' '),
            ];
        });

        return view('pages.print.etat', [
            'docLabel' => 'Liste des articles',
            'docNumber' => $depot->code ?? $depot->designation,
            'docDate' => now()->format('d/m/Y'),
            'backUrl' => route('gestions_articles.index'),
            'meta' => [
                ['Magazin', $depot->designation],
                ['Articles', $articles->count()],
            ],
            'columns' => [
                ['label' => '#', 'class' => 'inv-center', 'width' => '5%'],
                ['label' => 'Code', 'width' => '12%'],
                ['label' => 'Désignation', 'width' => '35%'],
                ['label' => 'Prix achat', 'class' => 'inv-num', 'width' => '12%'],
                ['label' => 'Prix vente', 'class' => 'inv-num', 'width' => '12%'],
                ['label' => 'Qté magazin', 'class' => 'inv-num', 'width' => '12%'],
                ['label' => 'Seuil', 'class' => 'inv-num', 'width' => '12%'],
            ],
            'rows' => $rows,
            'totals' => [
                ['Quantité en magazin', number_format($articles->sum(fn ($article) => $article->stocks->sum('quantite_disponible')), 0, ',', ' ')],
            ],
            'footerMessage' => 'Liste des articles — quantités du magazin ' . $depot->designation,
        ]);
    }
}
