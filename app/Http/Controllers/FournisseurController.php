<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fournisseurs = Fournisseur::all();
        return view('pages.fournisseurs.index', compact('fournisseurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.fournisseurs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'raison_sociale' => 'required|array|min:1',
            'raison_sociale.*' => 'nullable|string|max:255',
            'nom' => 'required|array|min:1',
            'nom.*' => 'nullable|string|max:255',
            'telephone' => 'required|array|min:1',
            'telephone.*' => 'nullable|string|max:255',
        ]);

        $raisonSociales = $request->input('raison_sociale', []);
        $noms = $request->input('nom', []);
        $telephones = $request->input('telephone', []);

        $lastId = Fournisseur::max('id') ?? 0;
        $createdCount = 0;

        $rows = max(count($raisonSociales), count($noms), count($telephones));

        for ($i = 0; $i < $rows; $i++) {
            $reason = trim($raisonSociales[$i] ?? '');
            $name = trim($noms[$i] ?? '');
            $phone = trim($telephones[$i] ?? '');

            if ($reason === '' && $name === '' && $phone === '') {
                continue;
            }

            $lastId++;
            $code = 'FRS-' . str_pad($lastId, 5, '0', STR_PAD_LEFT);

            Fournisseur::create([
                'code' => $code,
                'raison_sociale' => $reason !== '' ? $reason : null,
                'nom' => $name !== '' ? $name : null,
                'telephone' => $phone !== '' ? $phone : null,
            ]);

            $createdCount++;
        }

        $message = $createdCount > 1 ? "$createdCount fournisseurs ajoutés avec succès." : ($createdCount === 1 ? 'Fournisseur ajouté avec succès.' : 'Aucun fournisseur n’a été ajouté.');

        return redirect()->route('gestions_fournisseurs.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $fournisseur = Fournisseur::findOrFail($id);

        // Charger les achats avec leurs décaissements
        $achats = $fournisseur->achats()->with('decaissements')->orderBy('date_achat', 'desc')->get();

        // Calculer le montant total décaissé au fournisseur
        $montantTotalDecaisse = 0;
        $montantTotalASolder = 0;

        foreach ($achats as $achat) {
            foreach ($achat->decaissements as $decaissement) {
                $montantTotalDecaisse += $decaissement->montant_decaisse;
                $montantTotalASolder += $decaissement->reste;
            }
        }

        return view('pages.fournisseurs.show', compact('fournisseur', 'achats', 'montantTotalDecaisse', 'montantTotalASolder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $fournisseurFinds = Fournisseur::findOrFail($id);
        return view('pages.fournisseurs.edit', compact('fournisseurFinds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        $fournisseur->raison_sociale = $request->filled('raison_sociale') ? trim($request->raison_sociale) : null;
        $fournisseur->nom = $request->filled('nom') ? trim($request->nom) : null;
        $fournisseur->telephone = $request->filled('telephone') ? trim($request->telephone) : null;
        $fournisseur->save();

        return redirect()->route('gestions_fournisseurs.index')->with('success', 'Fournisseur mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        $fournisseur->delete();
        return redirect()->route('gestions_fournisseurs.index')->with('success', 'Fournisseur supprimé avec succès.');
    }

    /**
     * Import fournisseurs from CSV/Excel file
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
            $lastId = Fournisseur::max('id') ?? 0;

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
                    // Mapper les colonnes du CSV
                    // Format attendu: Type, Raison Sociale, Nom, Adresse, Téléphone, Email, Ville
                    $type = trim($row[0] ?? 'entreprise');
                    $raison_sociale = trim($row[1] ?? '');
                    $nom = trim($row[2] ?? '');
                    $telephone = trim($row[4] ?? '');

                    // Debug: Log des données lues (à retirer en production)
                    \Log::info('Import ligne', [
                        'row' => $row,
                        'type' => $type,
                        'raison_sociale' => $raison_sociale,
                        'nom' => $nom,
                        'telephone' => $telephone,
                    ]);

                    // Générer un code automatique unique
                    $lastId++;
                    $code = 'FRS-' . str_pad($lastId, 5, '0', STR_PAD_LEFT);

                    // Vérifier si le code existe déjà (sécurité)
                    while (Fournisseur::where('code', $code)->exists()) {
                        $lastId++;
                        $code = 'FRS-' . str_pad($lastId, 5, '0', STR_PAD_LEFT);
                    }

                    // Créer un nouveau fournisseur
                    Fournisseur::create([
                        'code' => $code,
                        'raison_sociale' => $raison_sociale !== '' ? $raison_sociale : null,
                        'nom' => $nom !== '' ? $nom : null,
                        'telephone' => $telephone !== '' ? $telephone : null,
                    ]);

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Ligne " . ($imported + 2) . ": " . $e->getMessage() . " | Données: " . implode(' | ', $row);
                }
            }

            fclose($handle);

            // Message de succès avec détails
            $message = "$imported fournisseur(s) importé(s) avec succès.";

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
     * Download template for fournisseur import
     */
    public function template()
    {
        $headers = ['Raison Sociale', 'Nom', 'Téléphone'];
        $filename = 'template_fournisseurs.csv';

        return response()->streamDownload(function () use ($headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            fclose($handle);
        }, $filename);
    }

    /**
     * Rechercher des fournisseurs pour Select2
     */
    public function search(Request $request)
    {
        $search = $request->input('search');

        $fournisseurs = Fournisseur::where('raison_sociale', 'LIKE', "%{$search}%")
            ->orWhere('nom', 'LIKE', "%{$search}%")
            ->orWhere('code', 'LIKE', "%{$search}%")
            ->orWhere('telephone', 'LIKE', "%{$search}%")
            ->limit(20)
            ->get(['id', 'raison_sociale', 'nom', 'code']);

        return response()->json($fournisseurs);
    }
}
