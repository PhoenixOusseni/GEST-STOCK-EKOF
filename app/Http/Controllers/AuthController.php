<?php

namespace App\Http\Controllers;

use App\Support\Acces;
use App\Support\DepotActif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Handle the user login request.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login_admin(Request $request)
    {
        // Valider les données reçues
        $credentials = $request->validate([
            'telephone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Tenter l'authentification
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate(); // Protection contre fixation de session

            $user = Auth::user();
            if ($user && $user->role === Acces::MAGAZIN) {
                if (! $user->depot_id) {
                    Auth::logout();

                    return back()->withErrors([
                        'telephone' => 'Aucun magazin n\'est affecté à ce compte.',
                    ])->onlyInput('telephone');
                }

                DepotActif::set((int) $user->depot_id);

                return redirect()->route('dashboard')->with('success', 'Connexion réussie');
            }

            return redirect()->route('depots.accueil')->with('success', 'Connexion réussie');
        }

        // Échec : identifiants invalides
        return back()->withErrors([
            'telephone' => 'Les identifiants sont invalides.',
        ])->onlyInput('telephone');
    }


    /**
     * Handle the user logout request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken(); // Protection CSRF

        return redirect()->route('login')->with('success', 'Déconnexion réussie.');
    }
}
