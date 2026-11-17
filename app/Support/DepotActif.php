<?php

namespace App\Support;

use App\Models\Achat;
use App\Models\AchatDetail;
use App\Models\BonCommande;
use App\Models\Decaissement;
use App\Models\Depot;
use App\Models\Encaissement;
use App\Models\Entree;
use App\Models\Inventaire;
use App\Models\Sortie;
use App\Models\Stock;
use App\Models\Vente;
use App\Models\VenteDetail;
use Carbon\Carbon;

class DepotActif
{
    public const SESSION_KEY = 'depot_actif_id';

    public static function id(): ?int
    {
        $id = session(self::SESSION_KEY);

        return $id ? (int) $id : null;
    }

    public static function current(): ?Depot
    {
        static $resolved = false;
        static $depot = null;
        if ($resolved) {
            return $depot;
        }
        $resolved = true;

        $id = self::id();
        if (!$id) {
            return null;
        }

        $depot = Depot::find($id);
        if (!$depot) {
            self::forget();

            return null;
        }

        return $depot;
    }

    public static function set(int $id): void
    {
        session([self::SESSION_KEY => $id]);
    }

    public static function forget(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public static function ventes()
    {
        return Vente::whereHas('details', fn ($q) => $q->where('depot_id', self::id()));
    }

    public static function bonsCommandes()
    {
        return BonCommande::whereHas('details', fn ($q) => $q->where('depot_id', self::id()));
    }

    public static function achats()
    {
        return Achat::whereHas('details', fn ($q) => $q->where('depot_id', self::id()));
    }

    public static function entrees()
    {
        return Entree::whereHas('details', fn ($q) => $q->where('depot_id', self::id()));
    }

    public static function sorties()
    {
        return Sortie::whereHas('details', fn ($q) => $q->where('depot_id', self::id()));
    }

    public static function inventaires()
    {
        return Inventaire::where('depot_id', self::id());
    }

    public static function stocks()
    {
        return Stock::where('depot_id', self::id());
    }

    public static function encaissements()
    {
        return Encaissement::whereHas('vente.details', fn ($q) => $q->where('depot_id', self::id()));
    }

    public static function decaissements()
    {
        return Decaissement::whereHas('achat.details', fn ($q) => $q->where('depot_id', self::id()));
    }

    public static function sommeVentes(Carbon $debut, Carbon $fin): float
    {
        return (float) VenteDetail::where('depot_id', self::id())
            ->whereHas('vente', fn ($q) => $q->whereBetween('date_vente', [$debut->toDateString(), $fin->toDateString()]))
            ->sum('prix_total');
    }

    public static function sommeAchats(Carbon $debut, Carbon $fin): float
    {
        return (float) AchatDetail::where('depot_id', self::id())
            ->whereHas('achat', fn ($q) => $q->whereBetween('date_achat', [$debut->toDateString(), $fin->toDateString()]))
            ->sum('prix_total');
    }
}
