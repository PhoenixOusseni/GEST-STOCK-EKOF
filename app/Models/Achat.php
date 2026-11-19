<?php

namespace App\Models;

use App\Models\Concerns\NoteAuteur;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Achat extends Model
{
    use HasFactory, NoteAuteur;

    protected $fillable = [
        'numero_achat',
        'designation',
        'date_achat',
        'montant_total',
        'montant_paye',
        'reste_a_payer',
        'fournisseur_id',
        'statut',
    ];

    protected $casts = [
        'date_achat' => 'date',
        'montant_total' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'reste_a_payer' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        // Générer automatiquement le numéro d'achat lors de la création
        static::creating(function ($achat) {
            if (empty($achat->numero_achat)) {
                $achat->numero_achat = self::generateNumeroAchat();
            }
        });
    }

    /**
     * Générer un numéro d'achat unique
     */
    public static function generateNumeroAchat()
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "PA/{$year}/{$month}/";

        $lastAchat = self::where('numero_achat', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastAchat ? intval(substr($lastAchat->numero_achat, -5)) + 1 : 1;
        return $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Relation avec le fournisseur
     */
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    /**
     * Relation avec les détails de l'achat
     */
    public function details()
    {
        return $this->hasMany(AchatDetail::class);
    }

    /**
     * Relation avec les décaissements
     */
    public function decaissements()
    {
        return $this->hasMany(Decaissement::class);
    }

    public function paiements()
    {
        return $this->hasMany(AchatPaiement::class)->orderBy('date_paiement');
    }
}

