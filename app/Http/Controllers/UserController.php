<?php

namespace App\Http\Controllers;

use App\Models\Depot;
use App\Models\User;
use App\Support\Acces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('depot')->paginate(15);
        return view('pages.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $depots = Depot::orderBy('designation')->get();

        return view('pages.users.create', compact('depots'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:255|unique:users,telephone',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:caisse,magazin,administrateur',
            'depot_id' => 'nullable|required_if:role,magazin|exists:depots,id',
        ]);

        User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'telephone' => $request->telephone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'depot_id' => $request->role === Acces::MAGAZIN ? $request->depot_id : null,
        ]);

        return redirect()->route('gestions_utilisateurs.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return view('pages.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $depots = Depot::orderBy('designation')->get();

        return view('pages.users.edit', compact('user', 'depots'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:255|unique:users,telephone,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:caisse,magazin,administrateur',
            'depot_id' => 'nullable|required_if:role,magazin|exists:depots,id',
        ]);

        $user->nom = $request->nom;
        $user->prenom = $request->prenom;
        $user->telephone = $request->telephone;
        $user->role = $request->role;
        $user->depot_id = $request->role === Acces::MAGAZIN ? $request->depot_id : null;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('gestions_utilisateurs.show', $user->id)
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('gestions_utilisateurs.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Show the profile of the authenticated user.
     */    public function profil()
    {
        $user = auth()->user();
        return view('pages.users.profil', compact('user'));
    }

    /**
     * Update the profile of the authenticated user.
     */
    public function updateProfil(Request $request, string $id)
    {
        if ((int) $id !== (int) auth()->id()) {
            abort(403);
        }

        $user = User::findOrFail($id);

        // Validation des données
        $rules = [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:255|unique:users,telephone,' . $user->id,
        ];

        // Si un nouveau mot de passe est fourni, on ajoute les règles de validation
        if ($request->filled('password')) {
            $rules['current_password'] = 'required';
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        // Vérifier le mot de passe actuel si un nouveau mot de passe est fourni
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                    ->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.'])
                    ->withInput();
            }
            $user->password = Hash::make($request->password);
        }

        $user->nom = $request->nom;
        $user->prenom = $request->prenom;
        $user->telephone = $request->telephone;

        $user->save();

        return redirect()->back()
            ->with('success', 'Profil mis à jour avec succès.');
    }

    // change password functionality can be added here if needed
    public function updatePassword(Request $request, string $id)
    {
        if ((int) $id !== (int) auth()->id()) {
            abort(403);
        }

        $user = User::findOrFail($id);

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.'])
                ->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()
            ->with('success', 'Mot de passe mis à jour avec succès.');
    }
}

