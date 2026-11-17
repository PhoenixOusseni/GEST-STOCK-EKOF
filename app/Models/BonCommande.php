<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BonCommande extends Model
{
    use HasFactory;

    protected $table = 'bons_commandes';

    protected $fillable = [
        'numero_bon',
        'date_commande',
        'client_id',
        'type_vente',
        'montant_total',
        'montant_paye',
        'reste_a_payer',
        'statut',
        'vente_id',
    ];

    protected $casts = [
        'date_commande' => 'date',
        'montant_total' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'reste_a_payer' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bon) {
            if (empty($bon->numero_bon)) {
                $bon->numero_bon = self::generateNumero();
            }
            if (empty($bon->statut)) {
                $bon->statut = 'en_cours';
            }
        });
    }

    public static function generateNumero(): string
    {
        $prefix = 'BC/' . date('Y') . '/' . date('m') . '/';
        $last = self::where('numero_bon', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $next = $last ? intval(substr($last->numero_bon, -5)) + 1 : 1;

        return $prefix . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function details()
    {
        return $this->hasMany(BonCommandeDetail::class);
    }

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function estValide(): bool
    {
        return $this->statut === 'valide';
    }
}
