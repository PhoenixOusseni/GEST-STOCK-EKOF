<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\User;
use Illuminate\Http\Request;

class ActiviteController extends Controller
{
    public function index(Request $request)
    {
        $query = Activite::with('user')->orderByDesc('id');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('q')) {
            $terme = $request->q;
            $query->where(function ($inner) use ($terme) {
                $inner->where('reference', 'like', '%' . $terme . '%')
                    ->orWhere('detail', 'like', '%' . $terme . '%');
            });
        }

        $activites = $query->paginate(30)->withQueryString();
        $users = User::orderBy('nom')->orderBy('prenom')->get();
        $actions = ['Création', 'Modification', 'Suppression'];
        $modules = Activite::query()->select('module')->distinct()->orderBy('module')->pluck('module');

        return view('pages.logs.index', compact('activites', 'users', 'actions', 'modules'));
    }
}
